<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use App\Models\Roleuser;
use DB;

class UserController extends Controller
{
    private $model;
    private $roleuser;


    public function __construct(User $model, Roleuser $roleuser)
    {
        $this->model = $model;
        $this->roleuser = $roleuser;
    }

    /**index */
    public function index(){
        $users = $this->model::with('roles')->get();
        return view('admin.pages.users.index', compact('users'));
    }

    public function create(){
        return view('admin.pages.users.form', ['disable_input' => false]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required',
                'phone' => 'required',
            ]
        );
        // process the login
        if ($validator->fails()) {
            return redirect('user.create')
                ->withErrors($validator)
                ->withInput();
        } else {
            // store
            $attributes = [
                'name' => $request->name,
                'email' => $request->email,
                'employee_no' => $request->employee_no,
                'phone' => $request->phone,
                'address' => $request->address,
                'postcode' => $request->postcode,
                'district' => $request->district,
                'gender' => $request->gender,
                'password' => bcrypt('mtsbd123'),
            ];
            //dd($attributes);
            $user = $this->model::create($attributes);

            //Insert roleuser table

            $roleAttr = [
                'role_id' => $request->role_id,
                'user_id' => $user->id,
            ];

            $roleuser = $this->roleuser::create($roleAttr);

            try {
                return redirect()->route('user_index')->with(['status' => 1, 'message' => 'Successfully created user']);
            } catch (\Exception $e) {
                //dd($e->errorInfo[2]);
                $errormsg = $e->errorInfo[2];
            }
        }
    }

    public function edit($id)
    {
        $user = $this->model::with('roles')->find($id);
        return view('admin.pages.users.form', ['user' => $user, 'disable_input' => false]);
    }

    public function editprofile($id)
    {
        $user = $this->model::with('roles')->find($id);
        if(auth()->user()->id == $id && auth()->user()->hasRoutePermission(\Route::currentRouteName())) {
            request()->attributes->add(['hasPermission' => true]);
        }else {
            request()->attributes->add(['hasPermission' => false]);
        }
        return view('admin.pages.users.form', ['user' => $user, 'disable_input' => true]);
    }

    public function update(Request $request)
    {
        $attributes = [
            'name' => $request->name,
            'email' => $request->email,
            'employee_no' => $request->employee_no,
            'phone' => $request->phone,
            'address' => $request->address,
            'postcode' => $request->postcode,
            'district' => $request->district,
            'gender' => $request->gender,
            //'password' => bcrypt('mtsbd123'),
        ];
        $user = $this->model::where('id', $request->id)->update($attributes);
        if($request->role_id){
            $roleAttr = [
                'role_id' => $request->role_id,
                'user_id' => $request->id,
            ];
            if(!empty($request->role_user_id)){
                $roleuser = $this->roleuser::where('id', $request->role_user_id)->update($roleAttr);
            } else {
                $roleuser = $this->roleuser::create($roleAttr);
            }
        }
        try {
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        } catch (\Exception $e) {
            return redirect()->route('user_edit', $request->id)->with(['status' => 0, 'message' => 'Error']);
        }
    }

    public function changePassword(Request $request){
        $password = $request->password;
        $cPassword = $request->confirm_password;
        if($password == $cPassword) {
            $attributes = [
                'password' => bcrypt($password),
            ];
            $user = $this->model::where('id', $request->id)->update($attributes);
            return redirect()->back()->with(['status' => 1, 'message' => 'Password changed Successfully']);
        }else {
            return redirect()->back()->with(['status' => 0, 'message' => 'Password and Conformed password is not matched']);
        }

    }

    public function destroy($id)
    {
        $user = $this->model::find($id);
        $user->delete();
        return redirect()->route('user_index', ['status' => 1, 'message' => 'Successfully deleted']);
    }


    /**
     * Api method
     *
     */
    public function apiGetUser(Request $request){
        $query = $this->model::query()->with('roles');

        $roles = ' $rolesu =[];
                    foreach($data->roles as $role){
                            $warehouseName = \App\Helpers\Query::accessModel("Warehouse")::name($role->warehouse_id) ?? null;
                            $roleName = \App\Helpers\Query::accessModel("Role")::name($role->role_id) ?? null;
                                $rolesu []= "<span title=\"{$warehouseName}\" class=\"badge bg-light text-dark\">{$roleName}</span>";
                    }
                    $roless = implode(" ", $rolesu);
                ';

        $field = [
            'button' => '
                    (auth()->user()->id == $data->id ? "<a></a>" : $this->ButtonSet::delete("user_destroy", $data->id)).
                    $this->ButtonSet::edit("user_edit", $data->id)',
            'name' => '$data->name',
            'email' => '$data->email',
            'employee_no' => '$data->employee_no',
            'phone' => '$data->phone',
            'employee_status' => '$data->employee_status',
            'roles'  => '$roless',
        ];
        //dd($field);


        return $this->Datatable::generate($request, $query, $field, ['phpcode' => $roles] );
    }
}
