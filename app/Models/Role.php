<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roles';

    protected $fillable =[
        'name',
        'code',
        'type',
        'is_mandatory'
    ];

    public static function name($role_id){
        return Role::where('id', $role_id)->first()->name ?? Null;
    }
    
    /**
     * Prevent deletion of mandatory roles
     */
    protected static function booted()
    {
        static::deleting(function ($role) {
            if ($role->is_mandatory) {
                throw new \Exception('Cannot delete mandatory role');
            }
        });
    }
} 
