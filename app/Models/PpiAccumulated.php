<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PpiAccumulated extends Model
{
    use HasFactory;
    public static function calculateTotalQty(array $ppiIds, int $productId): int
    {
        if ($productId === null || empty($ppiIds)) {
            return 0;
        }
        $ppiIds = array_filter(array_map('trim', $ppiIds));
        $cacheKey = 'ppi_total_qty_' . md5(implode(',', $ppiIds) . '_' . $productId);
    
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($ppiIds, $productId) {
            $sumQty = \DB::table('ppi_products')
                ->whereIn('ppi_id', $ppiIds)
                ->where('product_id', $productId)
                ->sum('qty');
    
            $sumBundleSize = \DB::table('ppi_bundle_products')
                ->whereIn('ppi_id', $ppiIds)
                ->where('product_id', $productId)
                ->sum('bundle_size');
    
            return $sumQty + $sumBundleSize;
        });
    }
    
    public static function calculateSpiProductTotalQty(array $spiIds, int $productId): int
    {
        if (empty($spiIds) || $productId === null) {
            return 0;
        }
    
        $spiIds = array_filter(array_map('trim', $spiIds)); // Clean up the array
    
        $cacheKey = 'spi_product_total_qty_' . md5(implode(',', $spiIds) . '_' . $productId);
    
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($spiIds, $productId) {
            // Sum only the qty in the spi_products table for the given ppiIds and productId
            return \DB::table('spi_products')
                ->whereIn('spi_id', $spiIds)
                ->where('product_id', $productId)
                ->sum('qty');
        });
    }





    // public function getTotalQtyAttribute()
    // {
    //     // Return 0 if no PPI IDs exist
    //     if (!$this->ppi_ids) {
    //         return [
    //             'total_qty' => 0
    //         ];
    //     }
    
    //     // Get PPI IDs as an array and clean up
    //     $ppiIds = explode(',', $this->ppi_ids);
    //     $ppiIds = array_map('trim', $ppiIds);  // Ensure no extra spaces
    //     $ppiIds = array_filter($ppiIds);  // Remove any empty values
    
    //     // If there are no valid PPI IDs, return 0
    //     if (empty($ppiIds)) {
    //         return [
    //             'total_qty' => 0
    //         ];
    //     }
    
    //     // Generate cache key
    //     $cacheKey = "ppi_total_qty_{$this->id}";
    
    //     // Use caching to store the result for 10 minutes
    //     return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($ppiIds) {
            
    //         // Debugging: Verify if $ppiIds is an array and has values
            
    
    //         // Sum quantities from the ppi_products table
    //         $sumQty = \DB::table('ppi_products')
    //             ->whereIn('ppi_id', $ppiIds)  // Change 'id' to 'ppi_id'
    //             ->where('product_id', $this->product_id)
    //             ->sum('qty');
                
    //         //dump($ppiIds);
    //         //dump($this->product_id);
            
    //         // Sum bundle sizes from the ppi_bundle_products table
    //         $sumBundleSize = \DB::table('ppi_bundle_products')
    //             ->whereIn('ppi_id', $ppiIds)
    //             ->where('product_id', $this->product_id)
    //             ->sum('bundle_size');
    
    //         // Return the total quantity
    //         return [
    //             'total_qty' => $sumQty + $sumBundleSize
    //         ];
    //     });
    // }


}
