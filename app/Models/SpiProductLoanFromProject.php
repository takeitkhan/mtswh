<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpiProductLoanFromProject extends Model
{
    use HasFactory;
    protected $table = 'spi_product_loan_from_projects';
    protected $fillable = [
        'spi_id', 'spi_product_id', 'product_id', 'ppi_id', 'ppi_product_id', 'original_project', 'original_project_id', 'landed_project', 'landed_project_id', 'qty', 'status', 'generate_ppi_id'
    ];
}
