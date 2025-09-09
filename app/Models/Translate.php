<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translate extends Model
{
    use HasFactory;
    protected $table = 'translates';
    protected $fillable = [
        'translate_for', 'for_id', 'lang', 'base_text', 'to_text'
    ];

    /**
     * @param $column
     * @param $option
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed|null
     */

    public static function getColumn($column, $option = []){
        $default = [
            'base_text' => null,
            'translate_for' => null,
            'for_id' => null,
            'lang' => null,
        ];

        $merge = array_merge($default, $option);
//        dd($merge);
        $query =  Translate::query();
        foreach ($merge as $index => $data){
            if(!empty($merge[$index])){
                $query = $query->where($index, $data);
            }
        }
        $query = $query->first();
        if($query){
            $query = $query->$column;
        }else {
            $query = null;
        }
        return $query;
    }
}
