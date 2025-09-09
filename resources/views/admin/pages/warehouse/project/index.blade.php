@extends('admin.layouts.master')

@section('title')
    Manage Project
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <!-- Form -->
        <div class="col-md-3">
            <h6>
                <div class="title-with-border">
                    @if(!empty($project))
                        <span class="text-primary">Edit Project Information</span>
                    @else
                        Project Information
                    @endif
                </div>
            </h6>
            <form action="{{ !empty($project) ? route('project_update') : route('project_store') }}" method="post">
                @csrf
                @if (!empty($project))
                    <input type="hidden" name="id" value="{{ $project->id }}">
                @endif
                <div class="form-group">
                    <label for="name">Project Name: </label>
                    <input type="text" class="form-control" placeholder="Enter project name" name="name"
                        value="{{ !empty($project) ? $project->name : old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="description">Project Type: </label>
                    <select name="type" id="" class="form-select">
                        <option value="">Select</option>
                        <option value="Supply" {{ !empty($project) && $project->type == 'Supply' ? 'selected' : '' }}>Supply</option>
                        <option value="Service" {{ !empty($project) && $project->type == 'Service' ? 'selected' : '' }}>Service</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name">Customer: </label>
                    <input type="text" class="form-control" placeholder="Enter project customer" name="customer"
                        value="{{ !empty($project) ? $project->customer : old('customer') }}">
                </div>

                <div class="form-group">
                    <label for="name">Vendor: </label>
                    <input type="text" class="form-control" placeholder="Enter project vendor" name="vendor"
                        value="{{ !empty($project) ? $project->vendor : old('vendor') }}">
                </div>

                <div class="form-group">
                    <label for="note">Note: </label>
                    <textarea class="form-control" name="note">{{ !empty($project) ? $project->note : old('note') }}</textarea>
                </div>
                <div class="form-submit_btn">
                    <button type="submit" class="btn blue">Submit</button>
                </div>
            </form>
        </div><!-- ENd Form-->
        <div class="col-md-1"></div>
        <!-- Data -->
        <div class="col-md-8 table-wrapper desktop-view mobile-view">
            <h6>
                <div class="title-with-border">
                    All Category
                    <div class="float-end">
                        <a href="{{route('project_import_mts_project')}}" class="btn btn-sm btn-outline-primary h-22 p-0 px-2">Import Project from MTS Project</a>
                    </div>
                </div>
            </h6>
            <table class="">
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Vendor</th>
                        <th>Customer</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    @php  $projects = $Query::accessModel('Project')::get(); @endphp
                    @foreach ($projects as $project)
                        <tr>
                            <td>
                                {!! $ButtonSet::delete('project_destroy', $project->id) !!}
                                {!! $ButtonSet::edit('project_edit', $project->id) !!}
                            </td>
                            <td>{{$project->id}}</td>
                            <td>{{$project->name}}</td>
                            <td>{{$project->code}}</td>
                            <td>{{$project->type}}</td>
                            <td>{{$project->vendor}}</td>
                            <td>{{$project->customer}}</td>
                            <td>{{$project->note}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
