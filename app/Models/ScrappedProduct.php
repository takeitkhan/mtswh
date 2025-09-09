<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrappedProduct extends Model
{
    use HasFactory;
    protected $table = 'scrapped_products';
}
