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
                        {{-- Select User Role --}}
                        @if(isset($disable_input) && $disable_input ==  false)
                        <div class="form-group select arrow_class">
                            <label for="select">Select Role </label>
                            @php
                                $roles = $Query::getData('roles')->whereIn('type', ['Global','General']);
                                $getExistingRoleUserColumnId = '';
                            @endphp
                            <select class="form-select" aria-label=".form-select-lg" id="select" name="role_id" required>
                                <option value="">Select Role</option>
                                @foreach ($roles as $role)
                                    @php
                                        if(!empty($user)){
                                            $user_role =  $user->roles->toArray();
                                        }else {
                                            $user_role = [];
                                        }
                                        $getMatchedRoleIdArr = array_search($role->id, array_column($user_role, 'role_id'));
                                        $getRoleId = $user->roles[$getMatchedRoleIdArr] ?? null;
                                        $userRoleId= $getRoleId->role_id ?? null;
                                        $getExistingRoleUserColumnId = $getRoleId->id ?? null;
                                    @endphp
                                    <option value="{{ $role->id }}"
                                        {{ $userRoleId === $role->id ? 'selected' : ''}}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="role_user_id" value="{{$getExistingRoleUserColumnId ?? Null}}" />
                        </div>
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
