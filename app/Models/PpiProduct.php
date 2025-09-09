<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiProduct extends Model
{
    use HasFactory;
    protected $table = 'ppi_products';
    protected $fillable = ['ppi_id', 'warehouse_id', 'product_id', 'qty', 'unit_price', 'price', 'product_state', 'health_status', 'note', 'action_performed_by'];

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

    /**
     * productInfo
     * Get Single Ppi Poduct Information
     * Request From Products Table
     * @param  mixed $product_id
     * @return void
     */
    public function SinglePpiProductInfo($product_id){
        $product = PpiProduct::where('product_id', $product_id)->first();
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
