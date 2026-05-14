<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
use App\Models\Role;
use App\Models\Routelistrole;

class RoleController extends Controller
{
    private $model;


    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function index(){
        $roles = $this->model::get();
        return view('admin.pages.roles.index', compact('roles'));
    }

    public function create(){
        return view('admin.pages.roles.form');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('role.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'name' => $request->name,
                'code' => strtolower(str_replace(' ', '_', $request->name)),
                'type' => $request->type,
            ];
            //dd($attributes);
            $role = $this->model::create($attributes);

            //Routelist Role
            if(!empty($request->route_id)){
                foreach($request->route_id as $key => $route){
                    $data = new Routelistrole();
                    $data->role_id = $role->id;
                    $data->route_id = $route;
                    $data->show_as = $request->show_as[$route] ?? NULL;
                    $data->save();
                }
            }

            try {
                return redirect()->route('role_index')->with(['status' => 1, 'message' => 'Successfully created']);
            } catch (\Exception $e) {
                //dd($e->errorInfo[2]);
                $errormsg = $e->errorInfo[2];
            }
        }
    }

    public function edit($id)
    {   
        $role = $this->model::find($id);        
        return view('admin.pages.roles.form', ['role' => $role]);
    }


    public function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $attributes = [
                'name' => $request->name,
                'code' => strtolower(str_replace(' ', '_', $request->name)),
                'type' => $request->type,
            ];
            $role = $this->model::where('id', $request->id)->update($attributes);

            //Routelist Role - Delete all existing permissions first
            Routelistrole::where('role_id', $request->id)->delete();
            
            // Only add back the checked permissions
            if(!empty($request->route_id)){
                foreach($request->route_id as $key => $route){
                    $data = new Routelistrole();
                    $data->role_id = $request->id;
                    $data->route_id = $route;
                    $data->show_as = $request->show_as[$route] ?? NULL;
                    $data->save();
                }
            }

            DB::commit();
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Role Update Error: ' . $e->getMessage(), [
                'role_id' => $request->id,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $role = $this->model::find($id);
        
        // Prevent deletion of mandatory roles
        if($role && $role->is_mandatory) {
            return redirect()->back()->with(['status' => 0, 'message' => 'Cannot delete mandatory role']);
        }
        
        $role->delete(); 
        return redirect()->back()->with(['status' => 1, 'message' => 'Successfully deleted']);
    }

}
