@extends('admin.layouts.master')

@section('title')
    PPI Product Use to
@endsection

@section('onlytitle')
    PPI Product Use to <span class="text-primary"></span>
@endsection

@section('content')
    <div class="content-wrapper" id="appVue">

        <div class="row">
            <div class="col-md-2">
                <div class="col-md-12 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                        <div class="card-body">
                            <p class="mb-4">Total</p>
                            <p class="font-30 mb-2">
                                @php

                                @endphp
                                <span id="total">

                                </span>
                                <span class="font-19">

                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-10">
                <div class="title-with-border mb-0 alert-secondary mb-2 px-2 text-dark border-0 fw-bold">
                    <div class="d-inline">List of Spi</div>
                        <table class="float-lg-end">
                            <tr class="bg-transparent">
                                <td>
                                    <input type="text" id="search_from_date" class="form-control form-control-sm datepicker" placeholder="From date" autocomplete="off">
                                </td>
                                <td>
                                    <input type="text" id="search_to_date" class="form-control form-control-sm datepicker" placeholder="To date" autocomplete="off">
                                </td>

                            </tr>
                        </table>
                </div>
                @php
                    $productStockHelper = new \App\Helpers\Warehouse\ProductStockHelper();
                    $getData = $productStockHelper->getSpiProductBasedOnPpiProductId($ppi_product_id);
                    request()->get('bundle_id') ? $getData = $getData->where('bundle_id', request()->get('bundle_id')) : null;
//                    dd($getData);
                @endphp
                <div class="row">
                    <div class="col-md-4 date_range">

                    </div>
{{--                    <div class="col-md-4 excel_btn mb-2">--}}

{{--                    </div>--}}
{{--                    <div class="col-md-4 search_bar">--}}

{{--                    </div>--}}
                </div>


                <table id="example" class="table table-sm" style="width:100%">
                    <thead>
                    <tr>
                        <th>PPI ID</th>
                        <th>SPI ID</th>
                        <th>Project Name</th>
                        <th>Product Name</th>
                        <th>Source</th>
                        <th>Qty</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($getData as $item)
                        @php
                            $getSpi = $Model('PpiSpi')::with('source')->where('id', $item->spi_id)->first();
                            $warehouse_code = $Model('Warehouse')::getColumn($getSpi->warehouse_id, 'code');
                        @endphp
                        <tr>
                            <td>{{$item->ppi_id}}</td>
                            <td>{{$item->spi_id}}</td>
                            <td>{{$item->project}}</td>
                            <td>{{$Model('Product')::name($item->product_id)}}</td>
                            <td>
                                <div class="crumbswrapper d-inline-block">
                                    <div class="crumbs mx-1 my-0 mt-1" id="source_breadcrumb">
                                            <?php foreach($getSpi->source as $source): ?>
                                        <div class="innerwrap">
                                                <span class="innerItem font-11  tdshow">
                                                    <span>{{$source->source_type}}:</span> {{$source->who_source}}
                                                </span>
                                        </div>
                                        <?php endforeach;?>
                                    </div>
                                </div>

                            </td>
                            <td>{{$item->qty}} </td>
                            <td>{{\Carbon\Carbon::parse($item->created_at)->isoFormat('Y-MM-DD')}}</td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot align="right">
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

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
            var table = $('#example').DataTable( {
                lengthChange: true,
                responsive: true,
                paging: false,
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Excel Export',
                        title: ' ({{date('d M Y')}})',
                        split: [ 'pdf', 'csv'],
                    }
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var sumColumns = [5]
                    sumColumns.forEach(function(colIndex){
                        // Total over all pages
                        var total = api
                            .column(colIndex)
                            .data()
                            .reduce( function (a, b) {
                                return parseInt(a) + parseInt(b);
                            }, 0 );

                        // Total over this page
                        var pageTotal = api
                            .column(colIndex, { page: 'current'} )
                            .data()
                            .reduce( function (a, b) {
                                return parseInt(a) + parseInt(b);
                            }, 0 );

                        // Update footer
                        $( api.columns(colIndex).footer() ).html(
                            pageTotal
                        );
                        $('#total').html(pageTotal)
                    })
                },
            } );

            table.buttons().container().appendTo( '#example_wrapper .col-md-6:eq(0)' );

            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var FilterStart =$('#search_from_date').val();
                    var FilterEnd = $('#search_to_date').val();
                    var DataTableStart = data[6].trim();
                    var DataTableEnd = data[6].trim();
                    if (FilterStart == '' || FilterEnd == '') {
                        return true;
                    }
                    if (DataTableStart >= FilterStart && DataTableEnd <= FilterEnd)
                    {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
            );

            // Datapicker
            $( ".datepicker" ).datepicker({
                "dateFormat": "yy-mm-dd",
                changeYear: true
            });

            // Search button
            $('#search_from_date').change(function(){
                table.draw();
            });
            $('#search_to_date').change(function(){
                table.draw();
            });

            // $('.excel_btn').html($('.dt-buttons').html())
            // $('.dt-buttons').empty()
            //
            // $('.search_bar').html(`<div class="dataTables_filter">
            //     ${$('.dataTables_filter').html()}</div>`)
            // $('#example_filter').empty()
            $('#example_wrapper > row').append('hkidjkj')

        } );



    </script>



    <style>
        .dt-buttons {
            display: inline-block;
            padding: 0px 10px;
            line-height: 12px;
            float: right;
        }
        .dataTables_length {
            xdisplay: inline-block;
        }
        a.buttons-excel, a.buttons-excel:hover {
            background: #4CAF50;
            display: inline-block;
            padding: 6px;
            color: #fff;
        }

        .crumbs .innerItem {
            white-space: normal;
        }
        .search_bar label {
            display: flex;
        }
    </style>
@endsection
