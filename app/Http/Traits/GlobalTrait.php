<?php

namespace App\Http\Traits;

trait GlobalTrait{

    protected $ButtonSet = '\App\Helpers\ButtonSet';
    protected $Query = '\App\Helpers\Query';
    protected $Datatable = '\App\Helpers\Datatable';
    protected $ApiCollection = '\App\Helpers\ApiCollection';

    public function Model($modelName){
        $modelPath = '\App\Models' . '\\' . $modelName;
        return $modelPath;
    }

    public function grantPermission($value = true){
       return  request()->attributes->add(['hasPermission' => $value]);
    }

}



?>
