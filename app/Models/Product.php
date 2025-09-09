<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $fillable = ['user_id', 'name', 'code', 'slug', 'description', 'brand_id', 'unit_id', 'warehouse_id', 'product_type', 'barcode_format', 'barcode_prefix', 'unique_key', 'stock_qty_alert', 'category_id'];



    /**
     * category
     * Make Relation
     * @return void
     */
    public function category(){
        return $this->hasOne('\App\Models\ProductCategory', 'id', 'category_id');
    }

    /**
     * thisWarehouseProduct
     * Current Entered Warehouse Product List
     * @return void
     */
    public static function thisWarehouseProducts($options = []){
        $default = [
            'product_type' => null,
        ];
        $merge = array_merge($default, $options);
        $product = Product::whereRaw("FIND_IN_SET('".request()->get('warehouse_id')."',warehouse_id)");
        if(!empty($merge['product_type'])){
            $product =  $product->whereRaw("FIND_IN_SET('".$merge['product_type']."',product_type)");
        }
        $product= $product->get();
        return $product;
    }

    public static function getColumn($product_id, $column){
        $value = Product::where('id', $product_id)->first();
        return $value->$column ?? NUll;
    }

    public static function name($product_id){
//        dd($product_id);
        $product = Product::where('id', $product_id)->first();
        return $product->name ?? Null;
    }
}
