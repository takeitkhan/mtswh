<?php 

namespace App\Http\Traits;


trait UserTrait{

    public function userInfoTrait(){
        return auth()->user()->userInfo();
    }
}



?>