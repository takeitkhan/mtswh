<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseVendor extends Model
{
    use HasFactory;
    protected $table = 'purchase_vendors';
    protected $fillable = [
        'spi_id', 'spi_product_id', 'vendor_id', 'vendor_name', 'qty', 'price','product_id', 'warehouse_id', 'create_ppi_id','create_ppi_product_id', 'action_performed_by'
    ];
}
