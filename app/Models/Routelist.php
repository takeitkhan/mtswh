<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Routelist extends Model
{
    use HasFactory;
    protected $table = 'route_lists';
    protected $fillable = [
        'route_title',
        'route_name',
        'route_parameter',
        'route_description',
        'route_group',
        'route_icon',
        'route_order',
        'route_hash',
        'show_menu',
        'parent_menu_id',
        'dashboard_position',
        'show_for',
    ];

    public function routeListRole(){
        $this->hasMany('App\Models\Routelistrole', 'id', 'route_id');
    }

  
}
