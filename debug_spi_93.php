<?php
require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$spi = \App\Models\Warehouse\Spi::find(93);
if (!$spi) {
    echo "SPI 93 not found\n";
    exit;
}

echo "========== SPI 93 ANALYSIS ==========\n";
echo "Warehouse Code: " . $spi->wh_code . "\n";
echo "Created By: " . $spi->created_by . "\n\n";

echo "========== SPI PRODUCTS ==========\n";
$spiProducts = $spi->products;
echo "Total Products in SPI: " . count($spiProducts) . "\n\n";

foreach($spiProducts as $sp) {
    echo "\n--- SPI Product ID: " . $sp->id . " ---\n";
    echo "Product ID: " . $sp->product_id . "\n";
    echo "PPI ID: " . $sp->ppi_id . "\n";
    echo "Current Qty: " . $sp->qty . "\n";
    
    // Get PPI total for this product
    $ppiTotal = DB::table('ppi_products')
        ->where('ppi_id', $sp->ppi_id)
        ->where('product_id', $sp->product_id)
        ->sum('qty');
    echo "PPI Total Qty: " . $ppiTotal . "\n";
    
    // Get all OTHER SPI rows for this same PPI+product
    $otherSpiRows = DB::table('spi_products')
        ->where('ppi_id', $sp->ppi_id)
        ->where('product_id', $sp->product_id)
        ->where('id', '!=', $sp->id)
        ->get(['id', 'qty']);
    
    $otherRowsQty = $otherSpiRows->sum('qty');
    echo "Other SPI Rows: " . count($otherSpiRows) . " (Total Qty: " . $otherRowsQty . ")\n";
    
    if ($otherSpiRows->isNotEmpty()) {
        foreach ($otherSpiRows as $row) {
            echo "  - SPI Product {$row->id}: {$row->qty}\n";
        }
    }
    
    $available = $ppiTotal - $otherRowsQty;
    echo "Available for THIS row: " . $available . " (Formula: $ppiTotal - $otherRowsQty)\n";
    
    // Show calculation for +2
    $newTotal = $sp->qty + 2;
    echo "\nIf we change from {$sp->qty} to 2 (increase of 2):\n";
    echo "  New Total Qty for this row: 2\n";
    echo "  New Available: " . ($ppiTotal - ($otherRowsQty + (2 - $sp->qty))) . "\n";
}
