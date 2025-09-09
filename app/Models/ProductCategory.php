<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $table = 'product_categories';
    protected $fillable = ['name', 'slug', 'parent_id', 'description', 'is_active'];

    //get value use id
    public static function name($id){
        $value = ProductCategory::where('id', $id)->first();
        return $value->name ?? NUll;
    }

    public static function getColumn($id, $columnName){
        $value = ProductCategory::where('id', $id)->first();
        return $value->$columnName ?? NUll;
    }
}
