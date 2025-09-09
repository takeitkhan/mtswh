@extends('admin.layouts.master')
@php
    $pageTitle = 'Manage Product of '. request()->get('warehouse_name');
@endphp
@section('title')
    {{$pageTitle}}
@endsection

@section('filter')
    <form action="{{ route('product_excel_store', request()->get('warehouse_code')) }}" method="post" enctype="multipart/form-data" class="d-inline-block me-3">
        @csrf
        @if(session()->get('importExcel'))
        <span class="badge alert-danger">Duplicate Product Code in Excel</span>
        <span class="badge alert-success">Product already exist in Database</span>
        <input type="text" class="d-none" name="start_import" value="{{session()->get('importExcel')}}" />
        <button type="submit" class="btn btn-sm btn-primary py-0 px-1 h-22 bw-1 upload-submit-btn">Start to import</button>
        @else
        <a class="d-inline-block" href="{{$publicDir}}/download/Warehouse-Product-Demo.xlsx"><i class="fa fa-download"></i>Excel Format</a>
        <div class="filename d-inline-block"></div>
        <label for="productIploadByExcel" class="btn btn-sm btn-outline-success py-0 px-1 h-22 bw-1" title="import as excel">
            <i class="las la-file-excel"></i>  Import
        </label>
        <input required type="file" name="excel_file" class="d-none filenames" id="productIploadByExcel">
        <button type="submit" class="btn btn-sm btn-primary border-primary py-0 px-1 h-22 bw-1 upload-submit-btn">Submit</button>
        @endif
    </form>

    <div id="dt_filter"></div>
@endsection


@section('content')

@php
    $wh_code = request()->get('warehouse_code');
@endphp
{{-- @dump(Session::get('importExcel') ?? null) --}}
<div class="table-wrapper desktop-view mobile-view">
    @if(session()->get('importExcel'))

    <table>
        <tr>
            <th>SL/N</th>
            <th>Product Name</th>
            <th>Product Code</th>
            <th>Product Type</th>
            <th>Category</th>
            <th>Unit</th>
            <th>Barcode</th>
            <th>Warehouse </th>
        </tr>
        @php
            $sessionProductImport = json_decode(session()->get('importExcel'), true);
        @endphp
        @foreach($sessionProductImport as $index => $product)
            @php
                $product = (object) $product;
                //dd($product);
                $code = str_replace(' ', '', $product->product_code);
                $checkHasDbCode = $Model('Product')::where('code', $code)->first();

                //Check Duplicate Code in Session
                $ck = array_keys(array_column($sessionProductImport, 'product_code'), $product->product_code);
                $classD = count($ck) > 1 ? 'bg-soft-danger' : null;

            $getWarehouseName = $Model('Warehouse')::whereIn('id', explode(',', $product->warehouse_id))->pluck('name')->implode(',');

            @endphp
            <tr style="{{$checkHasDbCode ? 'background-color: #d1e7dd;' : null}}" class="{{$classD ?? null}}">
                <td>{{++$index}}</td>
                <td>
                    {{$product->product_name}}
                </td>
                <td>{{$product->product_code}}</td>
                <td>{{$product->product_type ?? 'Supply,Service'}}</td>
                <td>{{$product->product_category}}</td>
                <td>{{$product->product_unit}}</td>
                <td>{{$product->product_barcode}}</td>

                <td>
                    {{ $product->warehouse_id ? $getWarehouseName : $Model('Warehouse')::pluck('name')->implode(',')}}
                </td>
            </tr>
        @endforeach
    </table>
    @else
    <table id="product" class="my-0" style="width:100%">

    </table>
    @endif
</div>


@endsection

@section('breadcrumb-bottom')
<div id="dt_pageinfo"></div>
@endsection

@section('cusjs')
    @include('components.datatable')

    <script>
        let  arr = [
                    {"data": "button"},
                    {"title": "Name",  "data": "name"},
                    {"title": "Code",  "data": "code" },
                    {"title": "Unit",  "data": "unit_id"},
                    {"title": "Brand",  "data": "brand_id"},
                    {"title": "Warehouse",  "data": "warehouse_code"},
                    {"title": "Category",  "data": "category"},
                    {"title": "Type",  "data": "product_type"},
                    {"title": "Barcode Format",  "data": "barcode_format"},
                    {"title": "Stock QTY Alert",  "data": "stock_qty_alert"},
                    {"title": "Created At",  "data": "created_at"},
                ];
        loadDatatable("#product", "{{ route('product_api_get', $wh_code) }}", arr);


    </script>

<script type="text/javascript">
    function fileUpload(options = {}){
        const defaults = {
            class : null,
            id : null,
            route : null,
            method: null,
        };
        const merge = jQuery.extend(defaults, options);
        $("input:file").change(function (){
            var fileName = $(this).val();
            $(merge.class).html(fileName);
            // $(ajax({
            //     url:
            //     method: merge.method,
            // }))
        });
    }
    fileUpload({class: ".filename"})
</script>
@endsection
