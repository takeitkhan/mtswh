<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpiSpiNotification extends Model
{
    use HasFactory;
    protected $table = 'ppi_spi_notifications';
    protected $fillable = [
        'status_id', 'is_read', 'action_performed_by'
    ];
}
