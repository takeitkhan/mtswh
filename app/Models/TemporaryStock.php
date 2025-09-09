<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryStock extends Model
{
    use HasFactory;

    protected $table = 'temporary_stocks';

    protected $fillable = [
        'action_format','product_id','ppi_spi_id', 'ppi_product_id','spi_product_id','waiting_stock_in','waiting_stock_out', 'warehouse_id'
    ];
}
