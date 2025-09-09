@extends('admin.layouts.master')

@section('title')
    Scrapped Product Report of {{$product->name}}
@endsection

@section('onlytitle')
    Scrapped Product of <span class="text-primary">{{$product->name}}</span>
@endsection

@section('content')
    <div class="content-wrapper" id="appVue">

        <div class="row">
            <div class="col-md-3">
                <div class="col-md-12 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                        <div class="card-body">
                            <p class="mb-4">Total</p>
                            <p class="font-30 mb-2">
                                @php
                                    $totals = $Model('ScrappedProduct')::where('id', $product->id)->first();
                                @endphp
                                <span id="total">{{$totals->scrapped_product + $totals->scrapped_product_bundle}} </span>
                                <span class="font-19">
                                    {{$Model("AttributeValue")::getValueById($product->unit_id)}}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="title-with-border mb-0 alert-secondary mb-2 px-2 text-dark border-0 fw-bold">
                    <div class="d-inline">List of Ppi</div>
                </div>
                @php
                    $ppis = $Model('PpiProduct')::leftjoin('ppi_bundle_products', 'ppi_bundle_products.ppi_product_id', 'ppi_products.id')
                            ->select('ppi_products.*', 'ppi_bundle_products.bundle_size')
                            ->where('ppi_products.product_id', $product->id)->where('ppi_products.health_status', 'Scrapped')->get()->groupBy('ppi_id')->toArray();
                    //dd($ppis);
                @endphp


                <table id="example" class="table table-sm" style="width:100%">
                    <thead>
                    <tr>
                        <th>PPI ID</th>
                        <th>Project Name</th>
                        <th>Source</th>
                        <th>Qty</th>
                        <th>Created at</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($ppis as $ppi_id => $ppi)
                        @php
                            $getPpi = $Model('PpiSpi')::with('source')->where('id', $ppi_id)->first();
                            $warehouse_code = $Model('Warehouse')::getColumn($getPpi->warehouse_id, 'code');
                        @endphp
                        <tr>
                            <td>{{$ppi_id}}</td>
                            <td>{{$getPpi->project}}</td>
                            <td>
                                <div class="crumbswrapper d-inline-block">
                                    <div class="crumbs mx-1 my-0 mt-1" id="source_breadcrumb">
                                            <?php foreach($getPpi->source as $source): ?>
                                        <div class="innerwrap">
                                                <span class="innerItem font-11  tdshow">
                                                    <span>{{$source->source_type}}:</span> {{$source->who_source}}
                                                </span>
                                        </div>
                                        <?php endforeach;?>
                                    </div>
                                </div>

                            </td>
                            <td>{{array_sum(array_column($ppi, 'qty'))  + array_sum(array_column($ppi, 'bundle_size'))}} </td>
                            <td>{{$getPpi->created_at->format('d M Y')}}</td>
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
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>



        <?php /*
        @foreach($ppis as $ppi_id => $ppi)
            <div class="col-lg-12 mb-2 font-11 bw-1 border-gray p-2 shadow-sm tr selectedRowIdbw0">
                @php
                    $getPpi = $Model('PpiSpi')::with('source')->where('id', $ppi_id)->first();
                    $warehouse_code = $Model('Warehouse')::getColumn($getPpi->warehouse_id, 'code');
                @endphp
                <a target="_blank" href="{{route('ppi_edit', [$warehouse_code, $ppi_id])}}">
                    <div class="">
                     <span class="td">
                        <div class="d-flex" style="justify-content: space-between;">
                            <div>
                                <span> <b>Ppi ID:</b> {{$ppi_id}} </span>
                                <span> <b>Qty:</b> {{array_sum(array_column($ppi, 'qty'))  + array_sum(array_column($ppi, 'bundle_size'))}} </span>

                                <span><b>Project:</b></span> {{$getPpi->project}}

                                <div class="crumbswrapper d-inline-block">
                                    <div class="crumbs mx-1 my-0 mt-1" id="source_breadcrumb">
                                        <?php foreach($getPpi->source as $source): ?>
                                            <div class="innerwrap">
                                                <span class="innerItem font-11  tdshow">
                                                    <span>{{$source->source_type}}:</span> {{$source->who_source}}
                                                </span>
                                            </div>
                                        <?php endforeach;?>
                                    </div>
                                </div>

                                <br>
                               <span><b> Created at : </b>{{$getPpi->created_at->format('d M Y')}}</span>
                            </div>
                        </div>
                     </span>
                </div>
                </a>
            </div>
        @endforeach
 */ ?>
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
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Excel Export',
                        title: 'Scrapped Product of 7/8 Feeder Cable Copper ({{date('d M Y')}})',
                        split: [ 'pdf', 'csv'],
                    }
                ],
                "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
                    var sumColumns = [3]
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

            table.buttons().container()
                .appendTo( '#example_wrapper .col-md-6:eq(0)' );
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
            display: inline-block;
        }
        a.buttons-excel, a.buttons-excel:hover {
            background: #4CAF50;
            padding: 6px;
            color: #fff;
        }
    </style>
@endsection
