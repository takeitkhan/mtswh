<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Role;
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
        $warehouses = Warehouse::all();
        return view('admin.pages.users.form', ['disable_input' => false, 'warehouses' => $warehouses]);
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
                'warehouse_id' => $request->warehouse_id ?? null,
            ];

            $roleuser = $this->roleuser::create($roleAttr);

            // Assign mandatory roles automatically
            $this->assignMandatoryRoles($user->id, $request->warehouse_id ?? null);

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
        $user = $this->model::with(['roles' => function($query) {
            $query->with('warehouse');
        }])->find($id);
        $warehouses = Warehouse::all();
        return view('admin.pages.users.form', ['user' => $user, 'disable_input' => false, 'warehouses' => $warehouses]);
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
            // Get the role to check if it's mandatory
            $role = Role::find($request->role_id);
            
            // Only update role if the current role is not mandatory
            // or if we're assigning a new role to a user without this role
            $existingRole = $this->roleuser::where('user_id', $request->id)
                                          ->where('role_id', $request->role_id)
                                          ->first();
            
            $roleAttr = [
                'role_id' => $request->role_id,
                'user_id' => $request->id,
                'warehouse_id' => $request->warehouse_id ?? null,
            ];
            if(!empty($request->role_user_id)){
                // Check if the role being updated is mandatory
                $oldRoleuser = $this->roleuser::find($request->role_user_id);
                $oldRole = Role::find($oldRoleuser->role_id);
                
                // Prevent removal of mandatory roles
                if($oldRole && $oldRole->is_mandatory) {
                    return redirect()->back()->with(['status' => 0, 'message' => 'Cannot modify mandatory role']);
                }
                
                $roleuser = $this->roleuser::where('id', $request->role_user_id)->update($roleAttr);
            } else {
                // Check if this role already exists for this user to avoid duplicate
                $alreadyExists = $this->roleuser::where('user_id', $request->id)
                                               ->where('role_id', $request->role_id)
                                               ->where('warehouse_id', $request->warehouse_id ?? null)
                                               ->first();
                
                if(!$alreadyExists) {
                    $roleuser = $this->roleuser::create($roleAttr);
                }
                // If already exists, just silently continue without error
            }
        }
        try {
            return redirect()->back()->with(['status' => 1, 'message' => 'Successfully updated']);
        } catch (\Exception $e) {
            return redirect()->route('user_edit', $request->id)->with(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
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
        
        // Check if user has any mandatory roles
        $mandatoryRoles = $this->roleuser::where('user_id', $id)
                                        ->whereHas('role', function($query) {
                                            $query->where('is_mandatory', 1);
                                        })
                                        ->get();
        
        if($mandatoryRoles->count() > 0) {
            return redirect()->route('user_index')->with(['status' => 0, 'message' => 'Cannot delete user with mandatory roles']);
        }
        
        $user->delete();
        return redirect()->route('user_index', ['status' => 1, 'message' => 'Successfully deleted']);
    }

    /**
     * Assign mandatory roles to a user
     */
    private function assignMandatoryRoles($userId, $warehouseId = null)
    {
        // Get all mandatory roles
        $mandatoryRoles = Role::where('is_mandatory', 1)->get();
        
        foreach($mandatoryRoles as $role) {
            // Check if user already has this role
            $existingRole = $this->roleuser::where('user_id', $userId)
                                          ->where('role_id', $role->id)
                                          ->first();
            
            // If not, create it
            if(!$existingRole) {
                $this->roleuser::create([
                    'role_id' => $role->id,
                    'user_id' => $userId,
                    'warehouse_id' => $warehouseId,
                ]);
            }
        }
    }

    /**
     * Api method
     *
     */
    public function apiGetUser(Request $request){
        $query = $this->model::query()->with('roles');

        $roles = ' $rolesu = [];
                    foreach($data->roles as $role){
                        $warehouseName = \App\Helpers\Query::accessModel("Warehouse")::name($role->warehouse_id) ?? "System-wide";
                        $roleName = \App\Helpers\Query::accessModel("Role")::name($role->role_id) ?? "Unknown";
                        $isMandatory = \App\Helpers\Query::accessModel("Role")::find($role->role_id)->is_mandatory ?? false;
                        
                        // Determine badge styling based on role type and mandatory status
                        $badgeClass = "bg-light text-dark";
                        if($isMandatory) {
                            $badgeClass = "bg-info text-white";
                        }
                        
                        $tooltip = "{$roleName}";
                        if($warehouseName && $warehouseName !== "System-wide") {
                            $tooltip .= " - {$warehouseName}";
                        }
                        if($isMandatory) {
                            $tooltip .= " (Mandatory)";
                        }
                        
                        $rolesu[] = "<span title=\"{$tooltip}\" class=\"badge {$badgeClass}\">{$roleName}</span>";
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
