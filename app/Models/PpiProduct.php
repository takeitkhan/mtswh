<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PpiProduct extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'ppi_products';
    protected $fillable = ['ppi_id', 'warehouse_id', 'product_id', 'qty', 'unit_price', 'price', 'product_state', 'health_status', 'note', 'action_performed_by'];
    
    // Specify the deleted_at column for SoftDeletes
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    public function ppiSpi(){
        return $this->hasOne('\App\Models\PpiSpi', 'id', 'ppi_id');
    }
    public function source(){
        return $this->hasMany('\App\Models\PpiSpiSource','ppi_spi_id', 'ppi_id');
    }
    public function ppiProduct(){
        return $this->hasOne('\App\Models\PpiProduct','id', 'id');
    }
    public function productInfo(){
        return $this->hasOne('\App\Models\Product','id', 'product_id');
    }
    public function productStock(){
        return $this->hasMany('\App\Models\ProductStock','ppi_spi_product_id', 'id');
    }
    public function bundles(){
        return $this->hasMany('\App\Models\PpiBundleProduct','ppi_bundle_products.ppi_product_id', 'id');
    }
    public function statuses(){
        return $this->hasMany('\App\Models\PpiSpiStatus','ppi_spi_product_id', 'id');
    }
    
    // Optional helper to directly get unit via productInfo
    public function unit()
    {
        return $this->hasOneThrough(
            AttributeValue::class, // final table
            Product::class,        // intermediate table
            'id',                  // Product table primary key
            'id',                  // AttributeValue primary key
            'product_id',          // local key on PpiProduct
            'unit_id'              // foreign key on Product pointing to AttributeValue
        );
    }

    /**
     * productInfo
     * Get Single Ppi Poduct Information
     * Request From Products Table
     * @param  mixed $product_id
     * @return void
     */
    public function SinglePpiProductInfo($product_id){
        $product = PpiProduct::where('product_id', $product_id)
                    ->whereNull('deleted_at')
                    ->first();
        return $product ?? Null;
    }


    /**
     * products
     * Get All Products
     * If Request by a Ppi id
     * @param  mixed $ppi_id
     * @return void
     */
    public static function products($ppi_id){
        $product = PpiProduct::leftjoin('products', 'products.id', 'ppi_products.product_id')
                    ->select('ppi_products.*', 'ppi_products.id as ppi_product_id', 'products.id as product_id', 'products.name as product_name', 'products.unit_id as product_unit_id', 'products.barcode_format as barcode_format')
                    ->where('ppi_products.ppi_id', $ppi_id)
                    ->whereNull('ppi_products.deleted_at')
                    ->get();
        return $product ?? Null;
    }


    /**
     * ppiProductInfoByPpiProductId
     * Get Ppi Product Info
     * @param  mixed $ppi_product_id
     * @return void
     */
    public static function ppiProductInfoByPpiProductId($ppi_product_id, $options =[]){
        $default = [
            'column' => null,
        ];
        $merge = array_merge($default, $options);
        $product = PpiProduct::leftjoin('products', 'products.id', 'ppi_products.product_id')
                    ->select('ppi_products.*',  'ppi_products.id as ppi_product_id', 'products.id as product_id', 'products.name as product_name', 'products.unit_id as product_unit_id', 'products.barcode_format as barcode_format')
                    ->where('ppi_products.id', $ppi_product_id)
                    ->whereNull('ppi_products.deleted_at')
                    ->first();
        if($product){
            if($merge['column']){
                $column = $merge['column'];
                return $product->$column;
            }else{
                return $product ?? Null;
            }
        }else {
            return null;
        }
    }
    
    public function getBundleTotal()
    {
        return \DB::table('ppi_bundle_products')
            ->where('ppi_id', $this->ppi_id)
            ->sum('bundle_size');
    }
}
