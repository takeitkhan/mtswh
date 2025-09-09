@extends('admin.layouts.master')

@section('title', 'All User')


@section('filter')

 <!-- Date Filter -->
 
    <div id="dt_filter"></div>

@endsection

@section('content')
<div class="p-0 content-wrapper">
    <div class="table-wrapper desktop-view mobile-view">
        <table id="example">
        
        </table>
    </div>
</div>
@endsection


@section('breadcrumb-bottom')
    <div id="dt_pageinfo"></div>
@endsection

@section('cusjs')

    @include('components.datatable')

    <script>
        let arr = [
            {"data" : "button"},
            {"title" : "Name",  "data" : 'name'},
            {"title" : "Email", "data" : 'email'},
            {"title" : "Employee_no", "data" : 'employee_no'},
            {"title" : "Phone", "data" : 'phone'},
            {"title" : "Employee Status", "data" : 'employee_status'},
            {"title" : "Roles", "data" : 'roles'},
        ];
   
        loadDatatable("#example", "{{ route('user_api_getuser') }}", arr);
    </script>

@endsection
