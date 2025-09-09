@extends('admin.layouts.master')


@section('title')
    Route Lists
   <?php 
    $t = new \App\Models\Routelist();
    //dd($t->getTable());
   ?>
@endsection

@section('filter')

 <!-- Date Filter -->
 
    <div id="dt_filter"></div>


@endsection


@section('content')

<div class="content-wrapper p-0">
    <div class="table-wrapper desktop-view mobile-view">
        <table id="example" class="my-0" style="width:100%">
             <thead>
                <tr>
                    <th></th>
                    <th>Route Title</th>
                    <th>Route Name</th>
                    <th>Route Group</th>
                    <th>Route Description</th>
                    <th>Route Order</th>
                    <th>Show in menu</th>
                    <th>Dashboard Menu</th>
                </tr>
            </thead>   
        </table>
    </div>
    <!-- Databale Test -->        
</div>

@endsection


@section('breadcrumb-bottom')
    <div id="dt_pageinfo"></div>
@endsection

@section('cusjs')

@include('components.datatable')

<script>
    let  arr = [
                { "data": "button"},
                { "data": "route_title"},
                { "data": "route_name" },
                { "data": "route_group"},
                { "data": "route_description"},
                { "data": "route_order"},
                { "data": "show_menu"},
                { "data": "dashboard_position"},
            ];
    loadDatatable("#example", "{{ route('routelist_api_get') }}", arr);
        
  
</script>





@endsection