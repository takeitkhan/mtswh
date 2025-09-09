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
 <table>
    <tr>
      <td>
         <input type='text' readonly id='search_from_date' class="datepicker" placeholder='From date'>
      </td>
      <td>
         <input type='text' readonly id='search_to_date' class="datepicker" placeholder='To date'>
      </td>
      <td>
         <input type='button' id="btn_search" value="Search">
      </td>
    </tr>
  </table>


    <div class="category-list-icon d-none">
        <ul>
            <li>
                <a href="javascript:void(0)"><i class="fas fa-file-pdf"></i></a>
            </li>
            <li>
                <a href="javascript:void(0)"><i class="fas fa-file-excel"></i></a>
            </li>

            <li>
                <a href="javascript:void(0)">
                    <i class="fas fa-list-ul"></i>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0)" class="dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-list-ul"></i>
                </a>
                <ul class="sub-menu dropdown-menu">
                    <li>
                        <a href="javascript:void(0)">menu 1</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)">menu 1</a>

                    </li>
                    <li>
                        <a href="javascript:void(0)">menu 1</a>
                    </li>
                </ul>
            </li>
        </ul>
        
    </div>
    <div id="dt_button"></div>
    <div id="dt_length" class="filter dataTables_wrapper"></div>
    <div id="dt_search"></div>
    <!-- search wrap -->
    {{-- <form action="">
        <div class="form-group search-wrap">
            <a href="javascript:void(0)">
                <i class="fas fa-search"></i>
            </a>
            <input id="input_search" class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        </div>
    </form> --}}




@endsection


@section('content')

<div class="content-wrapper p-0">
    <?php /*
    <div class="table-wrapper desktop-view mobile-view">
        <table id="table_id">
            <thead style="position: sticky;top:-1px;">
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
            <tbody>
                @foreach ($routelists as $routelist)
                    <tr>
                        <td>
                            {!! $ButtonSet::delete('routelist_destroy', $routelist->id) !!}
                            {!! $ButtonSet::edit('routelist_edit', $routelist->id) !!}
                        </td>
                        <td> {{ $routelist->route_title }} </td>
                        <td> {{ $routelist->route_name }} </td>
                        <td> {{ $Query::accessModel('Routelist')::routeGroupName($routelist->route_group) }} </td>
                        <td> {{ $routelist->route_description }} </td>
                        <td> {{ $routelist->route_order }} </td>
                        <td> {{ $routelist->show_menu }} </td>
                        <td> {{ $routelist->dashboard_position }} </td>

                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
    */ ?>
<!-- Datatable Test -->



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


@section('breadcrumb')
    <div id="dt_paginate"></div>
    <div id="dt_info"></div>
@endsection

@section('cusjs')


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap5.min.css">


<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>







<script>

    //Datatable
    function test(arg){
        $(document).ready(function() {

            let vals = [];
            //Ajax Call & Datatable
            let table =  $('#example').DataTable({
                "dom": 'tpiflB',
                "searching": true,
                "processing": true,
                "serverSide": true,
                "buttons": [ 'copy', 'excel', 'pdf', 'colvis' ],
                "pageLength" : parseInt(50),
                "lengthMenu": [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"] ],
                "ajax": {
                    "url": "{{ route('routelist_api_get') }}",
                    "type": "GET",
                    "dataSrc": function(s) {
                        // Format API response for DataTables
                        var response = s;
                        if (typeof s.response != 'undefined') {
                            response = s.response;
                        }
                        //console.log(JSON.stringify(response)); // Output from this is below...
                        return response.data;
                    },                    
                    "data" : function(data) {
                         // Read values
                        var from_date = $('#search_from_date').val();
                        var to_date = $('#search_to_date').val();

                        // Append to data
                        data.from_date = from_date;
                        data.to_date = to_date;
                        //console.log(from_date);
                        //
                        
                        for(i = 0; i < data.columns.length; i++){
                            vals.push(data.columns[i].data);
                            //val += { "data": data.columns[i].data}+',';
                        }
                        //console.log(data.columns);
                        //vals = val;
                    },
                    async: true,     
                },    

                "columns": [
                    { "data": "button"},
                    { "data": "route_title"},
                    { "data": "route_name" },
                    { "data": "route_group"},
                    { "data": "route_description"},
                    { "data": "route_order"},
                    { "data": "show_menu"},
                    { "data": "dashboard_position"},
                ],

                "language": {
                    //sLengthMenu: "Show _MENU_", // remove entries text
                    searchPlaceholder: 'Search',
                    emptyTable: "No record found",
                    search: ""
                },
            });

            // function da(){
            //     var v = [
            //         { "data": "button"},
            //         { "data": "route_title"},
            //         { "data": "route_name" },
            //         { "data": "route_group"},
            //         { "data": "route_description"},
            //         { "data": "route_order"},
            //         { "data": "show_menu"},
            //         { "data": "dashboard_position"},
            //     ];

            //     return v;
            // }
            function ra(vals){
                var s = '[';
                for(i = 0; i < vals.length; i++){
                   s += '{"data": "'+vals[i]+'"},';
                }
                s += ']'
                return eval(s);
            }

             console.log(ra(vals));
           
            // Datapicker 
            $( ".datepicker" ).datepicker({
                "dateFormat": "yy-mm-dd",
                changeYear: true
            });

            // Search button
            $('#btn_search').click(function(){
                table.draw();
            });

            //Position
            $("#dt_length").append($(".dataTables_length"));
            $("#dt_paginate").append($(".dataTables_paginate"));
            $('#dt_button').append($(".dt-buttons"));
            $('#dt_info').append($(".dataTables_info"));

            $('#dt_search').append($('.dataTables_filter'));

            $('.dt-buttons button').addClass('btn-sm')

            //Button Design
            $('.dt-buttons .buttons-copy span').html('<i class="fa fa-copy"></i>');
            $('.dt-buttons .buttons-excel span').html('<i class="fas fa-file-excel"></i>');
            $('.dt-buttons .buttons-pdf span').html('<i class="fas fa-file-pdf"></i>');
            $('.dt-buttons .buttons-colvis span').html('<i class="fa fa-arrow-down"></i>');
        
        });
    }
    test();


  
</script>




<style>
    #dt_button button.btn-secondary.buttons-copy,.buttons-pdf,.buttons-excel,.buttons-colvis {
        color: #333;
        background-color: transparent !important;
        border-color: transparent !important;
        padding: 1px 8px;
        margin-right: 5px;
    }
    #dt_button .btn-group .dt-buttons button:hover {
        background: #ddd;
        color: #333;
    }
    #dt_button .dt-buttons button:focus {
        background: #ddd;
        color: #333;
        box-shadow: unset;
    }
    #dt_button .btn-secondary:hover {
        color: #217df3;
    }
    #dt_search {
        text-align: center;
    }
    #dt_search input.form-control {
        outline: none;
        box-shadow: none;
        border: none;
        background: #fff;
        border: 1px solid #ccc;
        max-height: 22px;
        min-height: 22px;
        width: 300px;
    }
    #dt_button .dropdown-item.active, .dropdown-item:active{
        color: #333;
        text-decoration: none;
        background-color: #fff;
    }
    #dt_button div.dt-button-collection div.dropdown-menu {
        box-shadow: 0px 10px 15px #8686867d;
    }

    #dt_button .dropdown-item {
        display: block;
        width: 100%;
        padding: 2px 5px;
        clear: both;
        font-weight: 400;
        color: #212529;
        text-align: inherit;
        text-decoration: none;
        white-space: nowrap;
        background-color: #e6e6e6;
        border: 0;
    }
    #dt_button .dt-button-collection {
        right: 0;
        left: auto !important;
    }
    #dt_paginate .page-link {
        padding: 2px 10px;
    }

    #dt_paginate .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: transparent;
        border-color: #dee2e6;
    }

    #dt_paginate .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #0d6efd;
    }

    #dt_paginate .page-link {
        border: 0px solid #dee2e6;
        background-color: transparent;
    }
</style>











<script>
    
    // $(document).ready(function() {
    // var table = $('#example').DataTable( {
    //      "dom": 'tpilB',
    //      buttons: [ 'copy', 'excel', 'pdf', 'colvis' ],
    //      /*
    //     lengthChange: true,
    //     lenghth: 25,
    //     responsive: true,
    //     fixedColumns: true,
    //     fixedHeader: true,
    //     searching: true,
    //     ordering:  true,
    //     pagingType: "full_numbers",
    //     //select: true,
    //     buttons: [ 'copy', 'excel', 'pdf', 'colvis' ],
    //     */
    //     //
    //     /*
    //     "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
    //     "sPaginationType": "full_numbers",
    //             "bFilter":true,
    //             "sPageFirst": true,
    //             "sPageLast": true,
    //             "oLanguage": {
    //             "oPaginate": {
    //                 "sPrevious": "<< previous",
    //                 "sNext" : "Next >>",
    //                 "sFirst": "<<",
    //                 "sLast": ">>"
    //                 }
    //             },
    //         "bJQueryUI": true,
    //         "bLengthChange": true,
    //         "bInfo":true,
    //       //  "bSortable":true
    //             */
    // } );
    

    // //Serach Box
    // $('#input_search').keyup(function(){
    //     table.search($(this).val()).draw() ;
    // }),

    // //Lemgth
    
    // //Paginition

 
    // //
    // table.buttons().container()
    //     .appendTo( '#example_wrapper .col-md-6:eq(0)' );
    // } );
    
</script>





@endsection