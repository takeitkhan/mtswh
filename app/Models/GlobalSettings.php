<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalSettings extends Model
{
    use HasFactory;
    protected $table ='global_settings';
    protected $fillable = [
        'meta_title', 
        'meta_name', 
        'meat_value', 
        'meta_type', 
        'meta_group', 
        'meta_order', 
        'meta_placeholder'
    ];

    public static function getColumn($meta_name, $meta_value){
        $value = GlobalSettings::where('meta_name', $meta_name)->first();
        return $value->$meta_value ?? NUll;
    }
    
}
