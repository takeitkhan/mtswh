<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Warehouse extends Model
{
    use HasFactory;
    protected $table = 'warehouses';
    protected $fillable = [
        'name', 'location', 'code', 'phone', 'email', 'is_active'
    ];


    public function roleUsers(){
        return $this->hasMany('\App\Models\Roleuser', 'warehouse_id', 'id');
    }

    public static function name($warehouse_id){
        return Warehouse::where('id', $warehouse_id)->first()->name ?? Null;
    }

    public static function getNameByCode($warehouse_code){
        return Warehouse::where('code', $warehouse_code)->first()->name ?? Null;
    }

    public static function getColumn($id, $columnName){
        $value = Warehouse::where('id', $id)->first();
        return $value->$columnName ?? NUll;
    }
    
    // protected static function booted()
    // {
    //     static::addGlobalScope('active', function (Builder $builder) {
    //         $builder->where('is_active', 'Yes');
    //     });
    // }
}
