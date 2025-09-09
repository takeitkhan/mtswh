@extends('admin.layouts.master')

@section('title')
    Purchase Product Information
@endsection

@section('filter')
    <div id="dt_filter"></div>
@endsection


@section('content')
    @php
        $wh_code = request()->get('warehouse_code');
    @endphp

    <div class="table-wrapper desktop-view mobile-view">
        <table id="product" class="my-0" style="width:100%">

        </table>
    </div>

@endsection

@section('breadcrumb-bottom')
    <div id="dt_pageinfo"></div>
@endsection

@section('cusjs')
    @include('components.datatable')
    <script>
        let arr = [
            {"data": "button"},
            {"title": "ID", "data": "id"},
            {"title": "PPI Type", "data": "ppi_type"},
            {"title": "Status", "data": "ppi_last_status"},
            {"title": "Project", "data": "project"},
            {"title": "Transaction Type", "data": "tran_type"},
            {"title": "Sources", "data": "sources"},
            {"title": "Root Source", "data": "root_source"},
            {"title": "Action Performed By", "data": "action_performed_by"},
            {"title": "Created At", "data": "created_at"},
        ];
        loadDatatable("#product", "{{ route('ppi_api_get', $wh_code) }}", arr);
    </script>
@endsection
