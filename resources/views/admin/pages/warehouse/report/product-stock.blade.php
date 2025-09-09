@extends('admin.layouts.master')

@section('title')
    Product Stock Report
@endsection

@section('filter')
    <div id="dt_filter"></div>
@endsection

@section('content')
    <div class="table-wrapper desktop-view mobile-view">
        <table id="productStock" class="my-0" style="width:100%">

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
            {"title": "ID", "data": "id"},
            {"title": "Name", "data": "name"},
            {"title": "Code", "data": "code"},
            {"title": "Stock In", "data": "stock_in"},
            {"title": "Waiting Stock In", "data": "waiting_stock_in"},
            {"title": "Stock Out", "data": "stock_out"},
            {"title": "Waiting Stock Out", "data": "waiting_stock_out"},
            {"title": "Stock In Hand", "data": "stock_in_hand"},
            {"title": "Unit", "data": "unit"},
        ];
        loadDatatable("#productStock", "{{ route('report_api_get_product_stock') }}", arr);
        $('#dt_daterange').remove();
    </script>
@endsection
