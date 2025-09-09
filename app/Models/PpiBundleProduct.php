<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiBundleProduct extends Model
{
    use HasFactory;
    protected $table = 'ppi_bundle_products';
    protected $fillable = [
        'ppi_id',
        'ppi_product_id',
        'product_id',
        'warehouse_id',
        'bundle_name',
        'bundle_size',
        'bundle_price',
        'action_performed_by',
    ];
}
