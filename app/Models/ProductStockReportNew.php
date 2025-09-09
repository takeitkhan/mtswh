<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockReportNew extends Model
{
    use HasFactory;
    protected $table = 'stock_in_hand_new';

    /**
     * Has Product Stock
     */
    public static function stockInHand($product_id){
        $data =  ProductStockReportNew::where('product_id', $product_id)->first()->stock_in_hand ?? null;
        return $data;
    }
}
