<script>
    //Datatable
    function loadDatatable(tableId, route, dataAsArrObj, options = {}) {
        $(document).ready(function() {
            // console.log(tableId)
            let defaultArr  = {
                'paginate' : 50,
            };
            let arrmerge = $.extend(defaultArr, options);
            $(tableId).addClass('stripe dataTable')
            let vals = [];
            //Ajax Call & Datatable
            let table =  $(tableId).DataTable({
                "dom": 'tpiflB',
                "searching": true,
                "processing": true,
                'responsive': true,
                "serverSide": true,
                "initComplete": function () {
                    //console.log('@@@ init complete @@@');
                    //$(tableId+'tbody').removeClass("loading");
                    $('table'+tableId+' tr.loading').remove();
                },
                "order" : [],
                "buttons": [ 'copy', 'excel', 'pdf', 'colvis' ],
                "pageLength" : parseInt(arrmerge.paginate),
                "lengthMenu": [ [5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"] ],
                "ajax": {
                    "url": route,
                    "type": "GET",
                    "dataType" : "json",
                    "beforeSend": () => {
                        $('table'+tableId+ ' tbody tr').empty();
                        $('table'+tableId).prepend('<tr class="loading"><td>Loading.....</td></tr>')
                    },
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
                    },
                    async: true,
                    rowReorder: {
                        selector: 'td:nth-child(2)'
                    },

                },
                /*
                "columns": [
                    { "title": "", "data": "button"},
                    { "data": "route_title"},
                    { "data": "route_name" },
                    { "data": "route_group"},
                    { "data": "route_description"},
                    { "data": "route_order"},
                    { "data": "show_menu"},
                    { "data": "dashboard_position"},
                ],
                */
                    "columns" : dataAsArrObj,


                "language": {
                    //sLengthMenu: "Show _MENU_", // remove entries text
                    searchPlaceholder: 'Search',
                    emptyTable: "No record found",
                    search: "",
                },
            });


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

            //DateRange


        });
    }
</script>

<!-- filter -->

<script type="text/template" id="filter_tem">
    <div id="dt_daterange"></div>
    <div id="dt_button"></div>
    <div id="dt_length" class="filter dataTables_wrapper"></div>
    <div id="dt_search"></div>
</script>

<script>
    //Filter
      $('#dt_filter').html($("#filter_tem").html());
</script>

<!-- daterange -->
<script type="text/template" id="daterange_tem">
    <table>
        <tr class="bg-transparent">
          <td>
             <input type="text" id="search_from_date" class="form-control form-control-sm datepicker" placeholder="From date" autocomplete="off">
          </td>
          <td>
             <input type="text" id="search_to_date" class="form-control form-control-sm datepicker" placeholder="To date" autocomplete="off">
          </td>
          <td>
             <button type="button" id="btn_search" class="mr-2 button"> <i class="fa fa-search"></i> </button>
          </td>
        </tr>
      </table>
</script>

<script>

    let date_ranges = $("#daterange_tem").html();
    $("#dt_daterange").html(date_ranges);

</script>


<!-- Paginate with info -->

<script type="text/template" id="pageinfo_tem">
        <div id="dt_paginate"></div>
        <div id="dt_info"></div>
</script>

<script>
    $("#dt_pageinfo").html($("#pageinfo_tem").html());
</script>























<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap5.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

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

<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>




{{-- https://code.jquery.com/jquery-3.5.1.js
https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js
https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap.min.js
https://cdn.datatables.net/fixedheader/3.2.0/js/dataTables.fixedHeader.min.js
https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js
https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap.min.js --}}

<style>
    #dt_length {
        margin-right:  10px;
    }
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

     #dt_daterange input.form-control {
        outline: none;
        box-shadow: none;
        border: none;
        background: #fff;
        border: 1px solid #ccc;
        max-height: 22px;
        min-height: 22px;
        font-size:  12px;
        width: auto;
        margin-right: 5px;
        padding:  0px 5px;
    }
    #dt_daterange .button {
        box-shadow: none;
        border: 1px solid #ccc;
        max-height: 22px;
        min-height: 22px;
        padding:  0 5px;
        background-color: white;
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

    @media screen and (max-width: 767px){
        div.dt-buttons {
         width: auto;
        }
    }

    #dt_filter {
        display:  inherit;
    }

     #dt_pageinfo {
        display:  inherit;
    }

    table.dataTable thead th, table.dataTable thead td {
        padding: 8px 10px;
        border-bottom: 1px solid #e7e7e7;
    }

    table.dataTable tbody th, table.dataTable tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid #ededed;
        font-size: 14px;
    }
    table.dataTable.stripe tbody tr.odd, table.dataTable.display tbody tr.odd {
        background-color: #ffffff;
    }
    .table-wrapper table tbody td a.delete {
        display: inline-block;
        padding: 0px 10px;
    }

    .table-wrapper table thead tr {
        width: 100%;
        background: #ffffff;
    }

    table.dataTable.no-footer {
        border-bottom: 0px solid #ddd;
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #aaa;
        border-radius: 3px;
        padding: 0 4px;
        background-color: #ffffff;
    }
    table.dataTable thead{
        white-space: nowrap;
    }
</style>
