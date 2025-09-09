@extends('admin.layouts.master')

@section('title')

    {{ !empty($wh) ? 'Edit Warehouse' : 'Add new warehouse' }}

@endsection


@section('content')

    <div class="content-wrapper">
        <form action="{{ !empty($wh) ? route('warehouse_update') : route('warehouse_store') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-8 col-lg-3 col-sm-12">
                    <h6>
                        <div class="title-with-border">
                            Warehouse Information
                        </div>
                    </h6>
                    
                        @if (!empty($wh))
                            <input type="hidden" name="id" value="{{ $wh->id }}">
                        @endif
                        <div class="form-content">

                            <div class="form-group">
                                <label for="name">Name: </label>
                                <input type="text" class="form-control" id="name" placeholder="Enter name" name="name"
                                    value="{{ !empty($wh) ? $wh->name : old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Location: </label>
                                <input type="text" class="form-control" id="location" placeholder="Enter location"
                                    name="location" value="{{ !empty($wh) ? $wh->location : old('location') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Phone: </label>
                                <input type="number" class="form-control" id="phone" placeholder="Enter phone" name="phone"
                                    value="{{ !empty($wh) ? $wh->phone : old('phone') }}">
                            </div>

                            <div class="form-group">
                                <label>Email: </label>
                                <input type="email" class="form-control" id="email" placeholder="Enter email" name="email"
                                    value="{{ !empty($wh) ? $wh->email : old('email') }}">
                            </div>
                            <div class="form-group">
                                <label>Is Active:</label>
                                <select name="is_active" class="form-control">
                                    <option value="Yes" {{ (isset($wh) && $wh->is_active == 'Yes') || old('is_active', 'Yes') == 'Yes' ? 'selected' : '' }}>
                                        Yes
                                    </option>
                                    <option value="No" {{ (isset($wh) && $wh->is_active == 'No') || old('is_active') == 'No' ? 'selected' : '' }}>
                                        No
                                    </option>
                                </select>
                            </div>


                            <div class="form-submit_btn">
                                <button type="submit" class="btn blue">Submit</button>
                            </div>
                        </div>
                </div>
                <div class="col-lg-2"></div>
                <!-- -------------------
                    Assign User 
                ------------------------>

                <div class="col-lg-3">
                    <h6> 
                    <div class="title-with-border"> 
                        Assign user to role
                        <a href="javascript:void(0);" class="add_button d-inline-block float-end valign-text-bottom me-2" title="Add field"><i
                            class="fa fa-plus"></i></a>
                        </div> 
                    </h6>
                    @php
                        $users = $Query::getData('users');
                        $roles = $Query::getData('roles')->where('type','Custom');
                    @endphp
                    <div class="field_wrapper">
                        <?php if(!empty($assignedUser)){
                            foreach($assignedUser as $data){ 
                        ?>
                        <div>
                            <select name="assign_user[0{{$data->id}}][user_id]" class="select select-box" id="" required>
                                <option value="">Select user</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{!empty($data) && $user->id == $data->user_id ? 'selected' : ''}}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="assign_user[0{{$data->id}}][role_id]" class="select select-box" id="" required>
                                <option value="">Select role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{!empty($data) && $role->id == $data->role_id ? 'selected' : ''}}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <a href="javascript:void(0);" class="remove_button d-inline-block ms-3 valign-text-bottom" title="Remove field"><i
                                class="fa fa-times"></i></a>
                        </div>
                        <?php 
                            }
                        }?>
                    </div>

                </div> 
                <!-- End Assign User -->
                
            </div>
        </form>
    </div>

@endsection


@section('cusjs')

<script type="text/template" data-template="tem">
    
        <select class="select select-box assign_user_add_more" required>
            <option value="">Select user</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        <select class="select select-box assign_role_add_more" required>
            <option value="">Select role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
        <a href="javascript:void(0);" class="remove_button d-inline-block ms-3 valign-text-bottom" title="Remove field"><i
                class="fa fa-times"></i></a>
    
</script>

<script src="{{$viewDir}}/admin/pages/warehouse/assign-role.js?{{rand(0,999)}}"></script>
    <style>
        .content-wrapper .form-check,
        .content-wrapper .date input,
        .select2-container {
            padding-left: 0;
            width: 42% !important;
            margin-right: 10px;
        }
    </style>

@endsection
