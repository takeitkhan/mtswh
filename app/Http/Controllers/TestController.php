<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class TestController extends Controller
{
  
    
    public function index(){
        //dd(request()->get('authGeneralRole'));
        
        //dd($this->userInfoTrait());
        //return view('test.menu');
        //dd($this->userInfoTrait()->roles);
       
        //dd($this->userInfoTrait());
        $routeCollection =  app()->routes->getRoutes();

        return view('test.testview');
    }

}
