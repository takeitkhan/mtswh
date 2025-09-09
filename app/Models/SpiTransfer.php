<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiTransfer extends Model
{
    use HasFactory;
    protected $table = 'spi_transfers';
    protected  $fillable = ['spi_id', 'from_warehouse_id', 'ppi_id', 'to_warehouse_id'];
}
