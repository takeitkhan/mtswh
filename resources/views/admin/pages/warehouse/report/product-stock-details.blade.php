@extends('admin.layouts.master')

@php
   $productUnit = $Model("AttributeValue")::getValueById($product->unit_id);
   $wh_id = request()->get('wh_id');
   $project_name = request()->get('project');
   $set_url = url()->full();
@endphp

@section('title') Stock Report of {{$Model('Product')::name($product->id)}} @endsection
@section('onlytitle')
    Stock Report of <span class="text-primary">{{$Model('Product')::name($product->id)}}</span>

@endsection

@section('content')

    @php
        $productStockDetails = new \App\Helpers\Warehouse\PpiSpiProductStock();

        $ppiDetailsCollect = $productStockDetails->ppiStockDetails($product->id);
        $ppiDetails = $ppiDetailsCollect;

        if($wh_id){
            $ppiDetails = $ppiDetails->where('warehouse_id', $wh_id);
            $ppiDetailsCollect = $ppiDetailsCollect->where('warehouse_id', $wh_id);
        }

        if($project_name){
            $ppiDetails = $ppiDetails->where('project', $project_name);
            $ppiDetailsCollect = $ppiDetailsCollect->where('project', $project_name);
        }

        $stock_in = $ppiDetailsCollect->sum('stock_in_qty');
        $waiting_stock_in = $ppiDetailsCollect->whereNotNull('is_waiting_to_stock_in')->sum('product_qty');

        /**
        $spiDetailsCollect = $productStockDetails->spiStockDetails($product->id);
        $spiDetails = $spiDetailsCollect;

        if($project_name) {
            $spiDetails = $spiDetails->where('project', $project_name);
            $spiDetailsCollect = $productStockDetails->spiStockDetailsWithProjectName($product->id, $project_name);
            //$spiDetailsCollect = $spiDetailsCollect->where('project', $project_name);
        }
        
        //dd($spiDetailsCollect);

        if($wh_id){
            $spiDetails = $spiDetails->where('from_warehouse', $wh_id);
            $spiDetailsCollect = $spiDetailsCollect->where('from_warehouse', $wh_id);
        }
        **/
        
        if ($project_name) {
            $spiDetailsCollect = $productStockDetails->spiStockDetailsWithProjectName($product->id, $project_name);
        } else {
            $spiDetailsCollect = $productStockDetails->spiStockDetails($product->id);
        }
        
        $spiDetails = $spiDetailsCollect;
        
        if ($wh_id) {
            $spiDetails = $spiDetails->where('from_warehouse', $wh_id);
            $spiDetailsCollect = $spiDetailsCollect->where('from_warehouse', $wh_id);
        }

        $already_stock_out = $spiDetailsCollect->whereNull('is_waiting_to_stock_out')->sum('product_qty');
        $waiting_stock_out = $spiDetailsCollect->whereNotNull('is_waiting_to_stock_out')->sum('product_qty');
        $stock_in_hand = $stock_in - $already_stock_out - $waiting_stock_out;
    @endphp

    <div class="content-wrapper font-13">
        @if(request()->get('stock_in_hand_item'))
            @include('admin.pages.warehouse.report.stock_in_hand_item')
        @else
        <div class="row">
            <div class="col-xl-2">
                <div class="sticky-top">

                    <ul class="nav nav-pills my-0 justify-content-center mb-2" id="pills-tab" role="tablist">
                        <li class="nav-item " role="presentation">
                            <button class="nav-link active  border-1" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">PPI</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">SPI</button>
                        </li>
                    </ul>


                    @php
                        $counter []= ['name' => 'Stock In', 'qty' => $stock_in, 'css' => ' card-tale', 'link' => false];
                        $counter []= ['name' => 'Waiting to Stock In', 'qty' => $waiting_stock_in, 'css' => ' card-info', 'link' => false];
                        $counter []= ['name' => 'Stock Out', 'qty' => $already_stock_out,'css' => ' card-light-danger', 'link' => false];
                        $counter []= ['name' => 'Waiting To Stock Out', 'qty' => $waiting_stock_out < 0 ? 0 : $waiting_stock_out, 'css' => ' card-light-warning', 'link' => false];
                        $counter []= ['name' => 'Stock In Hand', 'qty' => $stock_in_hand < 0 ? 0 : $stock_in_hand , 'css' => ' card-light-blue', 'link' => url()->current().'?stock_in_hand_item=true'];
                    @endphp
                    @foreach($counter as $item)
                    {!! $item['link'] ? '<a target="_blank" href="'.$item['link'].'">' : null !!}
                        <div class="card mb-2 p-2 {{$item['css']}}">
                            <p class="mb-2">{{$item['name']}}</p>
                            <p class="font-18 mb-2"><span>{{$item['qty']}} </span><span class="font-14">{{$productUnit}}</span></p>
                        </div>
                    {!! $item['link'] ? '</a>' : null !!}
                    @endforeach

                    @if($project_name)
                        <form target="_blank" action="{{route('report_lended_from_project')}}?project={{request()->get('project')}}&&product_id={{$product->id}}" method="post">
                            @csrf
                            <input type="hidden" name="project" value="{{$project_name}}">
                            <input type="hidden" name="product_id" value="{{$product->id}}">
                            <input type="hidden" name="stock_in_hand" value="{{$stock_in_hand}}">
                            <div class="card card-light-blue p-2" style="background: #a700a2;">
                                    <p class="mb-2">
                                        Lended from Project (spi)
                                        <button class="btn py-0 px-1 btn-danger font-15" type="submit"><i class="fa fa-link"></i></button>
                                    </p>
                                    <div class="font-18 mb-1">
                                        <span class="font-17"> Processing:
                                            {{$Model('SpiProductLoanFromProject')::where('product_id', $product->id)
                                                     ->where('status', 'processing')
                                                     ->where('original_project', $project_name)
                                                     ->get('qty')->sum('qty') ?? 0
                                            }} {{$productUnit}}
                                        </span>
                                    </div>
                            </div>
                        </form>
                    @endif

                </div>
            </div>

            <div class="col-xl-7">

                <div class="tab-content" id="pills-tabContent">
                    <!-- ppi Start -->
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

                        <table id="ppiData" class="table table-sm table-bordered font-13" xstyle="width:100%">
                            <thead>
                            <tr>
                                <th style="width: 50px;">PPI ID</th>
                                <th style="width: 100px;">Project</th>
                                <th style="width: 100px;">Source</th>
                                <th>Qty</th>
                                <th>State</th>
                                <th>Warehouse</th>
                                <th>Date</th>
                            </tr>
                            <tbody>
                            @foreach($ppiDetails as $item)
                                <tr>
                                    <td style="{!! $item->is_waiting_to_stock_in ? 'background: #ffc10769;' : null  !!}{!! $item->stock_in_qty ? null : 'background: #cc000030;' !!}{!! $item->boos_approved ? null : 'background: #ffc10769;' !!}">
                                        <a title="ppi_product_id: {{$item->ppi_product_id}} product_id: {{$item->product_id}}"
                                        class="text-primary" target="_blank" href="{{route('ppi_edit', [$item->warehouse_code, $item->ppi_id])}}">
                                            {!! $item->transferable == 'yes' ? "<i title=\"transfer\" style=\"display: inline;border-radius: 100%;border: 1px solid #fd7e14;padding: 3px;font-size: 10px;\" class=\"fa fa-arrow-down text-orange\"></i>
                        " : null !!}
                                            {!! $item->purchase == 'yes' ? "<i title=\"purchase\" style=\"display: inline;border-radius: 100%;border: 1px solid #a0fd14;padding: 3px;font-size: 10px;\" class=\"fa fa-shopping-cart text-success\"></i>
                        " : null !!}
                                            {{$item->ppi_id}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$item->project}}
                                    </td>
                                    <td>
                                        <small>
                                            {!! str_replace(',', '<br>', $item->who_source) !!}
                                        </small>
                                    </td>
                                    <td><a class="text-primary" target="_blank"
                                           href="{{route('report_ppi_product_to_spi', $item->ppi_product_id)}}{{$item->bundle_id ? '?bundle_id='.$item->bundle_id : null }}"
                                        >{{$item->product_qty}}</td>
                                    <td>
                                        <small>
                                            <b>State:</b> {{$item->product_state}} <br>
                                            <b>Health:</b> {{$item->health_status}} <br>
                                            {!! $item->bundle_size ? "<b>Bundle Size :</b> {$item->bundle_size}" : NULL !!}
                                        </small>
                                    </td>
                                    <td>
                                        {{$item->warehouse_name}}
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($item->created_at)->format('d/m/Y')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>



                    </div><!-- End PPI -->



                    <!-- Spi Start -->
                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <table id="spiData" class="table table-sm table-bordered font-13" style="width:100%">
                            <thead>
                            <tr>
                                <th style="width: 50px;">Spi ID</th>
                                <th style="width: 100px;">Project</th>
                                <th style="width: 100px;">Source</th>
                                <th>Qty</th>
                                <th>State</th>
                                <th>Warehouse</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($spiDetails as $item)
                                <tr class="">
                                    <td style="
                                        {!! $item->is_waiting_to_stock_out ? 'background: #cc000030;' : null !!}
                                        {!! $item->boos_approved ? null : 'background: #ffc107;' !!}
                                        ">
                                        <a
                                            title="spi_product_id: {{$item->spi_product_id}} ppi_id: {{$item->ppi_id}} ppi_product_id: {{$item->ppi_product_id}} product_id: {{$item->product_id}}"
                                        class="text-primary" target="_blank" href="{{route('spi_edit', [$item->warehouse_code, $item->spi_id])}}">{{$item->spi_id}}</a>
                                    </td>
                                    <td>
                                        {{$item->original_project}} <br>
                                        <strong class="text-info font-weight-bold">{{ $item->lended_project ? 'Landed From '. $item->lended_project : null}}</strong>
                                    </td>
                                    <td>
                                        <small>
                                            {!! str_replace(',', '<br>', $item->who_source) !!}
                                        </small>
                                    </td>
                                    <td>{{$item->product_qty}}</td>
                                    <td>
                                        <small>
                                            <b>State:</b> {{$item->product_state}} <br>
                                            <b>Health:</b> {{$item->health_status}} <br>
                                            {!! $item->is_waiting_to_stock_out ? '<div class="alert-warning mt-2 p-1"><b> waiting to stock out</b></div>' : NULL !!}
                                        </small>
                                    </td>
                                    <td>
                                        {{$item->warehouse_name}} <br>
                                        {!! $item->from_warehouse_name ? "<strong class=\"text-info font-weight-bold\"> Lend From {$item->from_warehouse_name} </strong>" : NULL !!}
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($item->created_at)->format('d/m/Y')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- ENd Spi-->

                </div> <!-- ENd COl 10-->

            </div><!-- End Row -->

            <div class="col-xl-3">
                <div class="bg-white sticky-top">

                    <div class="list-group my-2">
                        <a href="{{url()->current()}}?project={{$project_name}}" class="font-13 list-group-item list-group-item-{{$wh_id == null ? 'success' : ''}}">Overall</a>
                        @foreach($Model('Warehouse')::get() as $warehouse)
                            <a href="{{'?wh_id='.$warehouse->id}}&&project={{$project_name}}" class="font-13 list-group-item list-group-item-{{$wh_id == $warehouse->id  ? 'success' : ''}}">
                                {{$warehouse->name}}
                            </a>
                        @endforeach
                    </div>
                    @include('admin.pages.warehouse.report.inc-product-stock-details.project')
                    <span class="mx-2">
                       <div class="d-inline-block">
                           <label class="d-block" for="" style="font-size: 12px;">Date range </label>
                           <div class="d-flex">
                               <input type="text" id="search_from_date" class="form-control form-control-sm datepicker me-2" placeholder="From date" autocomplete="off">
                                <input type="text" id="search_to_date" class="form-control form-control-sm datepicker" placeholder="To date" autocomplete="off">
                           </div>
                        </div>
                   </span>

                </div>
            </div><!-- End Row -->
        </div>
        @endif
    </div>



@endsection


@section('cusjs')



    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>



    <script>
        $(document).ready(function() {
            var tablePpi = $('#ppiData').DataTable( {
                lengthChange: true,
                responsive: true,
                "bPaginate": false, //hide pagination
                "bFilter": true, //hide Search bar
                "bInfo": false, // hide showing entries
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Excel Export',
                        title: '(Ppi) Stock Report Product of {{$Model('Product')::name($product->id)}} ({{date('d M Y')}})',
                        //split: [ 'pdf', 'csv'],
                    },
                ],
            } );

            tablePpi.buttons().container()
                .appendTo( '#ppiData_wrapper .col-md-6:eq(0)' );

            var tableSpi = $('#spiData').DataTable( {
                autoWidth: false,
                lengthChange: true,
                responsive: true,
                "bPaginate": false, //hide pagination
                "bFilter": true, //hide Search bar
                "bInfo": false, // hide showing entries
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Excel Export',
                        title: '(Spi) Stock Report Product of {{$Model('Product')::name($product->id)}} ({{date('d M Y')}})',
                        split: [ 'pdf', 'csv'],
                    }
                ],
            } );

            tableSpi.buttons().container()
                .appendTo( '#spiData_wrapper .col-md-6:eq(0)' );

            $(".datepicker").datepicker({
                dateFormat: "dd/mm/yy",
                changeYear: true
            });

            function dateRangePickerDataLoad(tableid) {
                //Search
                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {
                        var filterStart = $(`#search_from_date`).val();
                        var filterEnd = $(`#search_to_date`).val();
                        // Data in the table, assuming it's in the same "dd/mm/yy" format
                        var dataTableDate = data[6].trim();
                        if (!filterStart && !filterEnd) {
                            return true; // No filter applied
                        }
                        // Convert strings to Date objects
                        var startDate = filterStart ? $.datepicker.parseDate("dd/mm/yy", filterStart) : null;
                        var endDate = filterEnd ? $.datepicker.parseDate("dd/mm/yy", filterEnd) : null;
                        var tableDate = $.datepicker.parseDate("dd/mm/yy", dataTableDate);
                        // Compare dates
                        if ((!startDate || tableDate >= startDate) && (!endDate || tableDate <= endDate)) {
                            return true;
                        }
                        return false;
                    }
                );

                // Trigger table redraw on date change
                $(`#search_from_date`).change(function () {
                    tableid.draw();
                });
                $(`#search_to_date`).change(function () {
                    tableid.draw();
                });
            }
            dateRangePickerDataLoad(tablePpi);
            dateRangePickerDataLoad(tableSpi);
            //End Search

        });


    </script>

    <style>
        a.buttons-excel{
            display: inline-block;
        }
        .buttons-excel span{
            background: #0E9E60;
            padding: 5px;
            color: #fff;
        }
    </style>
@endsection
