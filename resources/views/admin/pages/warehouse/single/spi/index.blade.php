@extends('admin.layouts.master')

@section('title')
    Sales Product Information
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
            {"title": "SPI Type", "data": "spi_type"},
            {"title": "Status", "data": "spi_last_status"},
            {"title": "Project", "data": "project"},
            {"title": "Transaction Type", "data": "tran_type"},
            {"title": "Requested By", "data": "requested_by"},
            {"title": "To Whom", "data": "sources"},
            {"title": "To root",  "data": "root_source"},
            {"title": "Action Performed By", "data": "action_performed_by"},
            {"title": "Created At", "data": "created_at"},
        ];
        loadDatatable("#product", "{{ route('spi_api_get', $wh_code) }}", arr);


    </script>


@endsection
