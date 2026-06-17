<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixSpiProductQuantities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:spi-product-quantities {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix SPI product quantities that exceed PPI stock. Ensures SPI qty <= PPI total qty for each product.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 Running in DRY RUN mode - no changes will be made');
        } else {
            $this->warn('⚠️  Running in LIVE mode - changes will be made to database');
        }
        
        $this->newLine();
        
        // Get all SPI products with quantity issues
        $spiProducts = DB::table('spi_products as sp')
            ->select(
                'sp.id',
                'sp.spi_id',
                'sp.product_id',
                'sp.ppi_id',
                'sp.qty as spi_qty',
                DB::raw('COALESCE(SUM(pp.qty), 0) as ppi_total_qty')
            )
            ->leftJoin('ppi_products as pp', function($join) {
                $join->on('pp.ppi_id', '=', 'sp.ppi_id')
                     ->on('pp.product_id', '=', 'sp.product_id');
            })
            ->groupBy('sp.id', 'sp.spi_id', 'sp.product_id', 'sp.ppi_id', 'sp.qty')
            ->havingRaw('CAST(sp.qty AS UNSIGNED) > COALESCE(SUM(pp.qty), 0)')
            ->get();

        if ($spiProducts->isEmpty()) {
            $this->info('✅ No quantity issues found! All SPI quantities are correct.');
            return 0;
        }

        $this->error('❌ Found ' . $spiProducts->count() . ' SPI products with quantity issues:');
        $this->newLine();

        $fixedCount = 0;
        $skippedCount = 0;

        foreach ($spiProducts as $spiProduct) {
            $spiQty = (int)$spiProduct->spi_qty;
            $ppiTotalQty = (int)$spiProduct->ppi_total_qty;
            
            $this->line("SPI ID: {$spiProduct->spi_id}, Product ID: {$spiProduct->product_id}, PPI ID: {$spiProduct->ppi_id}");
            $this->line("  SPI Qty: {$spiQty}, PPI Total Qty: {$ppiTotalQty}");

            if ($spiQty > $ppiTotalQty) {
                $difference = $spiQty - $ppiTotalQty;
                $this->warn("  ⚠️  Shortfall: {$difference} units");

                if (!$isDryRun) {
                    // Update SPI product to match PPI total
                    DB::table('spi_products')
                        ->where('id', $spiProduct->id)
                        ->update([
                            'qty' => $ppiTotalQty,
                            'price' => $ppiTotalQty * DB::table('spi_products')->where('id', $spiProduct->id)->first()->unit_price ?? 0,
                            'updated_at' => now()
                        ]);
                    
                    $this->info("  ✅ Fixed: Qty adjusted from {$spiQty} to {$ppiTotalQty}");
                    $fixedCount++;
                } else {
                    $this->info("  [DRY RUN] Would fix: Qty adjusted from {$spiQty} to {$ppiTotalQty}");
                    $skippedCount++;
                }
            }

            $this->newLine();
        }

        $this->newLine();
        $this->info("Summary:");
        $this->line("  Total issues found: " . $spiProducts->count());
        $this->line("  Fixed: {$fixedCount}");
        $this->line("  Dry run (not applied): {$skippedCount}");

        if ($isDryRun) {
            $this->info('💡 Run without --dry-run to apply these fixes');
        }

        return 0;
    }
}
