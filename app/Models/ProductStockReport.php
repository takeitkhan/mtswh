<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockReport extends Model
{
    use HasFactory;
    protected $table = 'stock_in_hand';

    /**
     * Has Product Stock
     */
    public static function stockInHand($product_id) {
        $data =  ProductStockReport::where('product_id', $product_id)->first()->stock_in_hand ?? null;
        return $data;
    }
}
