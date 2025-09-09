<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routegroup extends Model
{
    use HasFactory;
    protected $table = 'route_groups';
    protected $fillable = [
        'name',
        'code',
    ];


    public function routelists(){
        $this->hasMany('App\Models\Routelist', 'id', 'route_group');
    }

    public static function name($group_id){
        $group = Routegroup::where('id', $group_id)->first();
        return $group->name ?? Null;
    }

    public static function accessColumn($group_id, $columnName){
        $group = Routegroup::where('id', $group_id)->first();
        return $group->$columnName ?? Null;
    }
}
