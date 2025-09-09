@extends('admin.layouts.master')

@section('title')
    Faulty Product
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
            {"title": "Total", "data": "scrapped_product"},
            {"title": "Unit", "data": "unit"},
        ];
        loadDatatable("#productStock", "{{ route('report_api_get_faulty_product') }}", arr);
        $('#dt_daterange').remove();
    </script>


@endsection

