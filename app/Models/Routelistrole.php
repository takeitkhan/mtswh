<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routelistrole extends Model
{
    use HasFactory;
    protected $table = 'route_list_roles';
    
    protected $fillable = [
        'role_id',
        'route_id',
        'show_as',
    ];

    public function routelist(){
        $this->hasMany('App\Models\Routelist', 'id', 'route_id');
    }

    public static function checkRouteRole($role_id, $route_id){
        $routeListRole = Routelistrole::where('role_id', $role_id)->where('route_id', $route_id)->first();
        return $routeListRole;
    }
}
