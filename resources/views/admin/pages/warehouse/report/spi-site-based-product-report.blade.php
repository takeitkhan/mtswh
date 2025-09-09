@extends('admin.layouts.master')

@section('title')
Product Report Of Site (Spi) - <b class="text-primary">{{request()->get('site_name')}}</b>
@endsection
@php
    $site_codes = \DB::SELECT("
    SELECT who_source FROM `ppi_spi_sources` WHERE source_type = 'Site' GROUP BY who_source;
    ");
@endphp
@section('filter')
<div id="dt_search">
    <form action="{{Request::url()}}" method="get">
        <label for="">Site Code &nbsp;</label>
{{--        <input name="site_name" value="{{request()->get('site_name')}}">--}}
        <select xid="search" name="site_name" class="form-control site_select">
            <option value=""></option>
            @foreach($site_codes as $d)
                <option value="{{$d->who_source}}" {{request()->get('site_name') == $d->who_source ? 'selected' : null}}>{{$d->who_source}}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-white px-2 py-0 h-22 w-auto"><i class="fa fa-search"></i></button>
    </form>
</div>
@endsection

@section('content')
    <div class="content-wrapper">

        @if(request()->get('site_name'))
            <div>

              <span class="mx-2">
                   <div class="d-inline-block">
                   <label class="d-block" for="" style="font-size: 12px;">Date range </label>
                    <form action="{{Request::url()}}" method="get">
                    <input type="hidden" name="site_name" value="{{request()->get('site_name')}}">
                       <div class="d-flex">
                           <input value="{{request()->get('from_date')}}" type="text" name="from_date" xid="search_from_date" class="form-control form-control-sm datepicker me-2" placeholder="From date" autocomplete="off">
                            <input value="{{request()->get('to_date')}}" type="text" name="to_date" xid="search_to_date" class="form-control form-control-sm datepicker" placeholder="To date" autocomplete="off">
                            <button style="height: 27px;border-radius: 0px;" type="submit" class="btn btn-primary px-2 py-0 h-22 w-auto"><i class="fa fa-search"></i></button>
                       </div>

                    </form>
               </div>
               </span>

            </div>

        @php
            $productStockHelper = new \App\Helpers\Warehouse\ProductStockHelper();
            $data = $productStockHelper->getSpiSiteBasedProduct(request()->get('site_name'), ['from_date' => request()->get('from_date') ?? false, 'to_date' => request()->get('to_date') ?? false] );
        @endphp
        <table id="example" class="table table-sm" style="width:100%">
            <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Spi ID</th>
                <th>Qty</th>

            </tr>
            </thead>
            <tbody>
            @foreach($data as $item)
                <tr>
                    <td>{{$item->id}}</td>
                    <td>{{$item->name}}</td>
                    <td>{!! $item->spi_id !!} </td>
                    <td> {{$item->qty}} </td>
                </tr>
            @endforeach
            </tbody>

            <tfoot align="right">
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
        @else
           <div class="row justify-content-center h-100">
               <div class="col-md-4 pt-5">
                   <div class="form-group">
                       <form action="{{Request::url()}}" method="GET">
                            <div class="inner-form">
                               <div class="input-field second-wrap">
    {{--                               <input id="search" name="site_name" type="text" placeholder="Enter A site Code">--}}
                                   <select xid="search" name="site_name" class="form-control site_select">
                                       <option value=""></option>
                                       @foreach($site_codes as $d)
                                           <option value="{{$d->who_source}}" {{request()->get('site_name') == $d->who_source ? 'selected' : null}}>{{$d->who_source}}</option>
                                       @endforeach
                                   </select>
                               </div>
                           <div class="input-field third-wrap">
                               <button class="btn-search" type="submit">
                                   <svg class="svg-inline--fa fa-search fa-w-16" aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                       <path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path>
                                   </svg>
                               </button>
                           </div>
                       </div>
                       </form>
                   </div>
               </div>
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
            var table = $('#example').DataTable( {
                lengthChange: true,
                responsive: true,
                paging: false,
                buttons: [
                    {
                        extend: 'excel',
                        text: 'Excel Export',
                        title: 'Product Report Of Site (Spi) - {{request()->get('site_name')}} ({{date('d M Y')}})',
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
            $('.site_select').select2({
                placeholder : 'Select a Site Code'
            })

            // Datapicker
            $( ".datepicker" ).datepicker({
                "dateFormat": "yy-mm-dd",
                changeYear: true
            });

            table.buttons().container()
                .appendTo( '#example_wrapper .col-md-6:eq(0)' );
        } );
    </script>







    <style>

        #dt_search select.form-control {
            outline: none;
            box-shadow: none;
            border: none;
            background: #fff;
            border: 1px solid #ccc;
            max-height: 22px;
            min-height: 22px;
            width: 300px;
        }

        #dt_search .select2-container .select2-selection--single {
            background: #fff;
        }
        #dt_search .select2-container {
            padding-left: 0;
            width: 300px !important;
        }
        .input-field .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529;
            line-height: 62px;
        }
        .input-field .select2-container .select2-selection--single {
            height: 68px;
            font-size: 23px;
        }


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

        .crumbs .innerItem {
            white-space: normal;
        }





        .inner-form {
            background: #fff;
            display: -ms-flexbox;
            display: flex;
            width: 100%;
            -ms-flex-pack: justify;
            justify-content: space-between;
            -ms-flex-align: center;
            align-items: center;
            box-shadow: 0 8px 20px 0 rgba(0,0,0,.15);
            border-radius: 3px;
        }

        .inner-form .input-field.second-wrap {
            -ms-flex-positive: 1;
            flex-grow: 1;
        }
        .inner-form .input-field {
            height: 68px;
        }
        .inner-form .input-field input {
            height: 100%;
            background: 0 0;
            border: 0;
            display: block;
            width: 100%;
            padding: 10px 32px;
            font-size: 16px;
            color: #555;
        }
        .inner-form .input-field input:hover, .s003 form .inner-form .input-field input:focus {
            box-shadow: none;
            outline: 0;
            border-color: #fff;
        }
        .inner-form .input-field.third-wrap {
            width: 74px;
        }
        .inner-form .input-field {
            height: 68px;
        }
        .inner-form .input-field.third-wrap .btn-search {
            height: 100%;
            width: 100%;
            white-space: nowrap;
            color: #fff;
            border: 0;
            cursor: pointer;
            background: #0d6efd;
            transition: all .2s ease-out,color .2s ease-out;
        }
        .inner-form .input-field.third-wrap .btn-search svg {
            width: 16px;
        }
    </style>


@endsection

