<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiProduct extends Model
{
    use HasFactory;

    protected $table = 'spi_products';
    protected $fillable = [
        'spi_id', 'warehouse_id', 'from_warehouse', 'product_id', 'ppi_product_id', 'ppi_id', 'bundle_id', 'qty', 'unit_price', 'price', 'note', 'action_performed_by', 'any_warning_cls'
    ];

    public function ppiSpi(){
        return $this->hasOne('\App\Models\PpiSpi', 'id', 'spi_id');
    }
    public function source(){
        return $this->hasMany('\App\Models\PpiSpiSource','ppi_spi_id', 'spi_id');
    }
    public function spiProduct(){
        return $this->hasOne('\App\Models\SpiProduct','id', 'id');
    }
    public function ppiProduct(){
        return $this->hasOne('\App\Models\PpiProduct','id', 'ppi_product_id');
    }
    public function productInfo(){
        return $this->hasOne('\App\Models\Product','id', 'product_id');
    }
    public function productStock(){
        return $this->hasMany('\App\Models\ProductStock','ppi_spi_product_id', 'id');
    }

    /**
     * products
     * Get All Products
     * If Request by a Ppi id
     * @param mixed $ppi_id
     * @return void
     */
    public static function products($spi_id)
    {
        $product = SpiProduct::leftjoin('products', 'products.id', 'spi_products.product_id')
            ->select('spi_products.*', 'spi_products.id as spi_product_id', 'products.id as product_id', 'products.name as product_name', 'products.unit_id as product_unit_id', 'products.barcode_format as barcode_format')
            ->where('spi_products.spi_id', $spi_id)
            ->get();
        return $product ?? Null;
    }


    public static function spiProductInfoBySpiProductId($spi_product_id, $options =[]){
        $default = [
            'column' => null,
        ];
        $merge = array_merge($default, $options);
        $product = SpiProduct::leftjoin('products', 'products.id', 'spi_products.product_id')
            ->select('spi_products.*',  'spi_products.id as spi_product_id', 'products.id as product_id', 'products.name as product_name', 'products.unit_id as product_unit_id', 'products.barcode_format as barcode_format')
            ->where('spi_products.id', $spi_product_id)
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

}
