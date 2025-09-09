<?php 
namespace App\Helpers;

class ApiCollection {  
    /**
     * initCurl
     *
     * @param  mixed $api_link
     * @return void
     */
    public static function initCurl($api_link){
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_URL, $api_link);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        $parsed_json = curl_exec($ci);
        $parsed_json = json_decode($parsed_json);
        curl_close($ci);
        return  $parsed_json;
    }
   
    /**
     * getMtsProject
     *
     * @return void
     */
    public static function getMtsProject(){
        $project = self::initCurl('https://tm.mtsbd.net/api/getproject');
        return $project;
    }
 
    /**
     * getMtsUser
     *
     * @return void
     */
    public static function getMtsUser(){
        $user = self::initCurl('https://tm.mtsbd.net/api/getuser');
        return $user;
    }

}
?>