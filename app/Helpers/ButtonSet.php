<?php
namespace App\Helpers;
use DB;
use App\Helpers\Query;

class ButtonSet {

    //Current user role
    public static function crole(){
        return auth()->user()->getUserRole();
    }

    //View Button
    public static function view($route_name, $id){
        //Check Route If have this user role is pernitted
        if(auth()->user()->checkUserRoleTypeGlobal() == true){
            $check = true;
        }else {
            $check = auth()->user()->checkRoute(Self::crole(), $route_name);
        }
        if(!empty($check)){
            $link= route($route_name, $id);
            $html = '<a title="View" class="xview font-14 text-teal" href="'.$link.'" title="View">';
            $html .=  '<span class="icon-eye"></span>';
            $html .=   '</a>';
            return $html;
        }
    }

    //Edit Button
    public static function edit($route_name, $id){
        //Check Route If have this user role is pernitted
        if(auth()->user()->checkUserRoleTypeGlobal() == true){
            $check = true;
        }else{
            $check = auth()->user()->checkRoute(Self::crole(), $route_name);
        }

        if(!empty($check)){
            $link= route($route_name, $id);
            $html = '<a title="Edit" class="edit text-purple font-14" href="'.$link.'" title="Edit">';
            $html .=  '<span class="icon-edit"></span>';
            $html .=   '</a>';
            return $html;
        }
    }

    //Delete
    public static function delete($route_name, $id, $anyAlertOptions = []){

        $default = [
            'class' => 'delete',
            'id' => 'delete',
        ];
        $merge = array_merge($default, $anyAlertOptions);
        //dd($merge);
        $randomC = rand(0,900);
        if(auth()->user()->checkUserRoleTypeGlobal() == true){
            $check = true;
        }else{
            $check = auth()->user()->checkRoute(Self::crole(), $route_name);
        }

        $anyAlert = [
            'message' => 'Are you sure to Delete',
            'action_btn_link' => route($route_name, $id),
            'action_btn_text' => 'Confirm',
        ];
        $replaceAnyAlert = array_replace($anyAlert, $anyAlertOptions);

        if(!empty($check)){
            //dd($check);
            $html = '<a id="'.$merge['id'].'" class="'.$merge['class'].'" title="Delete">';
            $html .=  Query::delete($route_name, $id, ['class' => $merge['class'], 'id' => $randomC.'the'.$merge['id']  ]);
            $html .=   '</a>';
            /*
            if(!empty($replaceAnyAlert['message'])){
                $html = '<a class="delete" title="Delete">';
                $html .=  self::alert($replaceAnyAlert['message'], $replaceAnyAlert['action_btn_link'], $replaceAnyAlert['action_btn_text'], '<i class="fas fa-times"></i>');
                $html .=   '</a>';
            }
            */
            return $html;
        }
    }

    //Button For Any Action
    public static function doAction($route, $options = []){
        $default = [
            'font-awesome' => 'fa fa-times',
            'style' => null,
            'label' => null,
            'class' => null,
            'title' => null,
        ];
        $arr = array_merge($default, $options);
        $html = "<a title=\"{$arr['title']}\" style=\"{$arr['style']}\" class=\"{$arr['class']}\" href=\"{$route}\"><i class=\"{$arr['font-awesome']}\"></i> {$arr['label']}</a>";

        return $html;
    }


    //Alert Button
    public static function alert($message, $action_btn_link = '#', $action_btn_text ="Ok", $icon){
        $html = '<button type="button" class="border-0" onclick="confirmAlertCustom(\''.$message.'\', \'\', \''.$action_btn_link.'\', \''.$action_btn_text.'\')';
        $html .= '">'.$icon.'</button>';
        return $html;
    }
}
