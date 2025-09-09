<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiSetProduct extends Model
{
    use HasFactory;
    protected $table = 'ppi_set_products';
    protected $fillable = ['ppi_id', 'warehouse_id', 'set_name', 'ppi_product_id', 'action_performed_by'];


    public static function getSet($product_id){
        $set = PpiSetProduct::whereRaw("find_in_set('".$product_id."', ppi_product_id)")->get();
        return $set;
    }

    public static function getSetByPpi($ppi_id){
        $set = PpiSetProduct::where('ppi_id', $ppi_id)->get();
        return $set;
    }
}

