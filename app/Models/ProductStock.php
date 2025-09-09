<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DB;
class ProductStock extends Model
{
    use HasFactory;
    protected $table = 'product_stocks';
    protected $fillable = [
        'ppi_spi_id', 'action_format', 'product_id', 'bundle_id', 'ppi_spi_product_id', 'barcode', 'original_barcode', 'product_unique_key', 'stock_action', 'stock_type', 'qty', 'entry_date',  'warehouse_id', 'action_performed_by', 'note'
    ];

    public function ppiSpi(){
        return $this->hasOne('\App\Models\PpiSpi', 'id', 'ppi_spi_id');
    }
    public function source(){
        return $this->hasMany('\App\Models\PpiSpiSource','ppi_spi_id', 'ppi_spi_id');
    }
    public function ppiProduct(){
        return $this->hasOne('\App\Models\PpiProduct','id', 'ppi_spi_product_id');
    }

    public function spiProduct(){
        return $this->hasOne('\App\Models\SpiProduct','id', 'ppi_spi_product_id');
    }

    public function productInfo(){
        return $this->hasOne('\App\Models\Product','id', 'product_id');
    }


//$ppi_spi_id,
//$ppi_spi_product_id,
    public static function checkStock($product_id, $action_format, $stock_action, $options = []){
        $default = [
            'orginal_barcode' => null,
            'bundle_id' => null,
            'warehouse_id' => null,
            'ppi_spi_id' => null,
            'ppi_spi_product_id' => null,
            'getColumn' => null,
            'data' => null,
        ];
        $merge =  array_merge($default, $options);
        $query =  ProductStock::where('action_format', $action_format)
                        ->where('product_id', $product_id)
                        ->where('stock_action', $stock_action);
        if($merge['ppi_spi_id']){
            $query = $query->where('ppi_spi_id', $merge['ppi_spi_id']);
        }
        if($merge['ppi_spi_product_id']){
            $query = $query->where('ppi_spi_product_id', $merge['ppi_spi_product_id']);
        }
        if($merge['warehouse_id']){
            $query = $query->where('warehouse_id', $merge['warehouse_id']);
        }
        if($merge['orginal_barcode']){
            $query = $query->where('orginal_barcode', $merge['orginal_barcode']);
        }
        if($merge['bundle_id']){
            $query = $query->where('bundle_id', $merge['bundle_id']);
        }
        if($merge['getColumn']){
            $query = $query->first();
            return $query->$merge['getColumn'];
        }
        if($merge['data']){
            $query = $query->first();
            return $query;
        }
        $get = $query->get()->sum('qty');
//        dump($get);
        return $get ?? 0;
    }


}
