<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyPpiSoftDeletes extends Command
{
    protected $signature = 'verify:ppi-soft-deletes {--product=2 : Product ID to check} {--show-deleted : Show deleted PPIs too}';
    protected $description = '✅ Verify PPI soft-delete mechanism is working correctly';

    public function handle()
    {
        $productId = $this->option('product');
        $showDeleted = $this->option('show-deleted');

        $this->info("\n=== PPI SOFT-DELETE VERIFICATION ===\n");
        $this->line("Product ID: $productId");

        // Check active PPIs
        $activePpis = DB::table('ppi_products')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->get();

        $this->info("\n✅ ACTIVE PPIs (visible in lists):");
        if ($activePpis->isEmpty()) {
            $this->warn("   No active PPIs found");
        } else {
            foreach ($activePpis as $ppi) {
                $this->line("   • PPI {$ppi->ppi_id} (ID: {$ppi->id}) - Stock: {$ppi->qty}");
            }
        }

        // Check deleted PPIs
        if ($showDeleted) {
            $deletedPpis = DB::table('ppi_products')
                ->where('product_id', $productId)
                ->whereNotNull('deleted_at')
                ->get();

            $this->warn("\n🗑️  SOFT-DELETED PPIs (hidden from lists):");
            if ($deletedPpis->isEmpty()) {
                $this->line("   No deleted PPIs");
            } else {
                foreach ($deletedPpis as $ppi) {
                    $this->line("   • PPI {$ppi->ppi_id} (ID: {$ppi->id}) - Deleted: {$ppi->deleted_at}");
                }
            }
        }

        // Test the actual API query
        $this->newLine();
        $this->info("📊 Testing actual API query (getPpiListForProduct equivalent):");
        $ppis = DB::table('ppi_products')
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->select('id', 'ppi_id', 'product_id', 'qty')
            ->get();

        $this->line("   API would return: " . count($ppis) . " PPIs");
        foreach ($ppis as $ppi) {
            $this->line("   ✓ PPI {$ppi->ppi_id} with qty {$ppi->qty}");
        }

        // Check database structure
        $this->newLine();
        $this->info("🔧 Database Structure Check:");
        $columns = DB::select("DESCRIBE ppi_products");
        $hasDeletedAt = false;
        foreach ($columns as $col) {
            if ($col->Field === 'deleted_at') {
                $hasDeletedAt = true;
                $this->line("   ✓ deleted_at column exists (Type: {$col->Type})");
            }
        }
        if (!$hasDeletedAt) {
            $this->error("   ✗ deleted_at column MISSING!");
        }

        // Check views
        $this->newLine();
        $this->info("👁️  Database Views Check:");
        $views = DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='warehouse12' AND TABLE_TYPE='VIEW' AND TABLE_NAME LIKE '%ppi%'");
        foreach ($views as $view) {
            $this->line("   • {$view->TABLE_NAME}");
        }

        $this->newLine();
        $this->info("✅ Verification complete!\n");
        
        return 0;
    }
}
