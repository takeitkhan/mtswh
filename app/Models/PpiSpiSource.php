<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiSpiSource extends Model
{
    use HasFactory;
    protected $table = 'ppi_spi_sources';
    protected $fillable = [
        'ppi_spi_id', 'action_format', 'source_type', 'who_source', 'who_source_id', 'levels', 'warehouse_id'
    ];
}
