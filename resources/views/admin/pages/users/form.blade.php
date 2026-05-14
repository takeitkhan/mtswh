@extends('admin.layouts.master')

@section('title')

    {{ !empty($user) ? 'Edit user' : 'Add new user' }}

@endsection


@section('content')
@php
    //dd(array_search(4, array_column($user->roles->toArray(), 'role_id')));
@endphp
    <!-- form content -->
    <div class="content-wrapper">

        <div class="row">
            <div class="col-md-8 col-lg-3 col-sm-12">
                <form action="{{ !empty($user) ? route('user_update') : route('user_store') }}" method="post">
                    @csrf
                    @if (!empty($user))
                        <input type="hidden" name="id" value="{{ $user->id }}">
                    @endif
                    <div class="form-content">

                        <div class="form-group name">
                            <label for="name">Name: </label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name"
                                value="{{ !empty($user) ? $user->name : old('name') }}" required>
                        </div>

                        <div class="form-group email">
                            <label for="email">Email: </label>
                            <input {{$disable_input == true ? 'readonly' : null}} type="email" class="form-control" id="email" aria-describedby="emailHelp"
                                placeholder="Enter email" name="email"
                                value="{{ !empty($user) ? $user->email : old('email') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="phoneNumber">Phone No: </label>
                            <input type="number" class="form-control" id="phoneNumber" placeholder="Phone number"
                                name="phone" value="{{ !empty($user) ? $user->phone : old('phone') }}" required>
                        </div>

                        <div class="form-group">
                            <label>Employee No: </label>
                            <input {{$disable_input == true ? 'readonly' : null}} type="text" class="form-control" placeholder="Employee No" name="employee_no"
                                value="{{ !empty($user) ? $user->employee_no : old('employee_no') }}">
                        </div>

                        <div class="form-group">
                            <label>Address: </label>
                            <input type="text" class="form-control" placeholder="Enter Address" name="address"
                                value="{{ !empty($user) ? $user->address : old('address') }}">
                        </div>

                        <div class="form-group">
                            <label>Post code: </label>
                            <input type="text" class="form-control" placeholder="Enter post code" name="postcode"
                                value="{{ !empty($user) ? $user->postcode : old('postcode') }}">
                        </div>

                        <div class="form-group">
                            <label>District: </label>
                            <input type="text" class="form-control" placeholder="Enter District" name="district"
                                value="{{ !empty($user) ? $user->district : old('district') }}">
                        </div>

                        <div class="form-group select arrow_class">
                            <label for="select">Gender </label>
                            @php
                                $genders = [
                                    'Male' => 'Male',
                                    'Female' => 'Female',
                                ];
                            @endphp
                            <select class="form-select" name="gender">
                                <option value="">Select gender</option>
                                @foreach ($genders as $index => $gender)
                                    <option value="{{ $index }}"
                                        {{ !empty($user) && $user->gender == $index ? 'selected' : '' }}>
                                        {{ $gender }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Select User Warehouse --}}
                        @if(isset($disable_input) && $disable_input ==  false)
                        <div class="form-group select arrow_class">
                            <label for="warehouse_id">Select Warehouse </label>
                            @php
                                $selectedWarehouseId = null;
                                if(!empty($user)){
                                    if(!empty($user->roles) && $user->roles->count() > 0){
                                        // Find the first role with a non-null warehouse_id
                                        $userRole = $user->roles->firstWhere('warehouse_id', '!=', null) ?? $user->roles->first();
                                        if($userRole && $userRole->warehouse_id){
                                            $selectedWarehouseId = (int) $userRole->warehouse_id;
                                        }
                                    }
                                }
                            @endphp
                            <select class="form-select" aria-label=".form-select-lg" id="warehouse_id" name="warehouse_id">
                                <option value="">Select Warehouse</option>
                                @if(isset($warehouses) && count($warehouses) > 0)
                                    @foreach ($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}"
                                            {{ (int)$warehouse->id === $selectedWarehouseId ? 'selected' : ''}}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="" disabled>No warehouses available</option>
                                @endif
                            </select>
                        </div>
                        @endif
                        {{-- End User Warehouse --}}

                        {{-- Select User Role --}}
                        @if(isset($disable_input) && $disable_input ==  false)
                        <div class="form-group select arrow_class">
                            <label for="select">Select Role 
                                <small style="color: #666;">(User role is automatically assigned)</small>
                            </label>
                            @php
                                // Include all role types except mandatory roles (they're auto-assigned)
                                $roles = $Query::getData('roles')
                                    ->where('is_mandatory', false)
                                    ->sortBy(function($role) {
                                        return $role->type . '_' . $role->name;
                                    });
                                $getExistingRoleUserColumnId = '';
                                $selectedRoleId = '';
                                
                                // Find the user's non-mandatory role for warehouse assignment
                                if(!empty($user) && $user->roles->count() > 0) {
                                    foreach($user->roles as $userRole) {
                                        $roleObj = $Query::accessModel('Role')::find($userRole->role_id);
                                        if($roleObj && !$roleObj->is_mandatory) {
                                            $selectedRoleId = $userRole->role_id;
                                            $getExistingRoleUserColumnId = $userRole->id;
                                            break; // Take the first non-mandatory role
                                        }
                                    }
                                }
                            @endphp
                            <select class="form-select" aria-label=".form-select-lg" id="select" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ $selectedRoleId == $role->id ? 'selected' : ''}}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="role_user_id" value="{{$getExistingRoleUserColumnId ?? Null}}" />
                        </div>
                        
                        <!-- Display mandatory roles -->
                        @php
                            $mandatoryRoles = $Query::getData('roles')->where('is_mandatory', true);
                        @endphp
                        @if($mandatoryRoles->count() > 0)
                        <div class="alert alert-info" style="margin-top: 10px;">
                            <strong>Mandatory Roles:</strong>
                            <br>
                            @foreach ($mandatoryRoles as $mandatoryRole)
                                <span class="badge bg-primary">{{ $mandatoryRole->name }}</span>
                            @endforeach
                            <br>
                            <small>These roles are automatically assigned to all users and cannot be removed.</small>
                        </div>
                        @endif
                        @endif
                        {{-- End User Role --}}
                        <div class="form-submit_btn">
                            <button type="submit" class="btn blue">Submit</button>
                        </div>
                    </div>

                </form>
            </div>

            <div class="col-md-1"></div>
            @if (!empty($user))
            <div class="col-md-4 col-lg-3 col-sm-12">
                <div class="form-content">
                    <form action="{{route('user_change_password')}}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <div class="form-group name">
                            <label for="name">Password </label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Password" name="password"
                                   value="" required>
                        </div>

                        <div class="form-group name">
                            <label for="name">Confirm Password </label>
                            <input type="text" class="form-control" id="name" placeholder="Confirm Password" name="confirm_password"
                                   value="" required>
                        </div>

                        <div class="form-submit_btn">
                            <button type="submit" class="btn blue w-auto px-3">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>



    </div>

@endsection
