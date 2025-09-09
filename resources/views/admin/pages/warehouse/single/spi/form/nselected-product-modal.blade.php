<div class="selected-product-modal" id="reload_modal">

    <?php
    // $checkStock = $Model('ProductStock')::
    //                                    leftjoin('ppi_spis', 'ppi_spis.id', 'product_stocks.ppi_spi_id')
    //                                    ->select('ppi_spis.*', 'product_stocks.*')
    //                                    ->where('product_stocks.action_format', 'Ppi')
    //                                    ->where('product_stocks.product_id', $product_id)
    //                                    ->get();

    $checkStock = function ($ppi_spi_type) use ($Model, $product_id, $warehouse_id, $spi_project) {
        return $Model('ProductStock')::with('ppiSpi', 'source', 'ppiProduct', 'productInfo')
            ->where('action_format', 'Ppi')
            ->where('product_id', $product_id)
            ->where('warehouse_id', $warehouse_id)
            ->whereHas('ppiSpi', function ($q) use ($ppi_spi_type, $spi_project) {
                //$q->where('ppi_spi_type', $ppi_spi_type)
                 $q->where('project', 'LIKE', '%'.$spi_project.'%')
                ;
            })
            //->groupBy('ppi_spi_id')
            ->groupBy('ppi_spi_product_id')
            ->get();
    };

    //echo $product_id;
    //echo request()->get('warehouse_id');
    //dd($checkStock('Service'));
    //    dd($checkStock($browse));
    //    echo $browsePpi;
    //
    //dump($checkStock($browse));
    ?>

    <div class="modal_sub_header_wrap">
        <div class="warehouse_list bg-warning bg-gradient">
            @php
                $warehouses = $Model('Warehouse')::where('is_active', 'Yes')->get();
            @endphp
            @foreach($warehouses as $warehouse)
                <button class="btn btn_browse btn-sm rounded-0 {{$warehouse_id == $warehouse->id ? 'btn-primary' : ''}}"
                        data-warehouse_id="{{ $warehouse->id ?? NULL }}"
                        data-browse="{{$browse}}"
                        data-spi_project="{{$spi_project}}"
                        data-original_project="{{$original_project}}"
                        data-landed_project="{{$landed_project}}"
                        data-row_id=" {{$row_id}}"
                        data-warehouse_code="{{$warehouse->code}}"
                        data-product_id="{{$product_id}}">
                    {{ $warehouse->name ?? NULL }}
                </button>
            @endforeach
        </div>

        <div class="button_set d-flex align-items-center">
            <div class="flex-fill text-start text-secondary">
                <strong title="product_id: {{$product_id}}">{{$Model('Product')::getColumn($product_id, 'name')}}</strong> |
                <small class="font-11 fw-bold">Product Code:{{$Model('Product')::getColumn($product_id, 'code')}}</small> |
                @php
//                    $productStockHelper = new \App\Helpers\Warehouse\ProductStockHelper();
//                    $getProductStockProject = $productStockHelper->getProductStock($product_id);
//                    $getProductStockProject = $getProductStockProject->where('have_stock_status', 'yes')
//                                        ->where('product_id',  $product_id)
//                                        ->where('project', 'LTE modernization')
//                                        ->where('warehouse_id', $warehouse_id)
//                                        ->sum('stock_out');
//                    dump($getProductStockProject);
                @endphp
                <select name="" id="" class="select_a_project xselect-box">
                    <option value="">Select project</option>
                    @foreach($allProject as $value)
                        @php
                            $productStockHelper = new \App\Helpers\Warehouse\ProductStockHelper();
                            $getProductStockProject = $productStockHelper->getProductStock($product_id);
                            $getProductStockProject = $getProductStockProject->where('have_stock_status', 'yes')
                                                ->where('product_id',  $product_id)
                                                ->where('project', $value->name)
                                                ->where('warehouse_id', $warehouse_id)
                                                ->sum('stock_in_hand');


                            $checkTemporary = \App\Models\TemporaryStock::leftjoin('spi_products', 'spi_products.id', 'temporary_stocks.spi_product_id')
                                                    ->leftjoin('ppi_spis', 'ppi_spis.id', 'spi_products.ppi_id')
                                                     ->select('ppi_spis.project', 'temporary_stocks.waiting_stock_out', 'ppi_spis.warehouse_id')
                                                    ->where('temporary_stocks.product_id', $product_id)
                                                    ->where('spi_products.from_warehouse', $warehouse_id)
                                                    ->where('ppi_spis.project',  $value->name)
                                                    ->sum('temporary_stocks.waiting_stock_out');

                            /*
                            $checkTemporary = \DB::SELECT("
                                SELECT temporary_stocks.*, ppi_spis.project
                                    FROM `temporary_stocks`
                                    LEFT JOIN ppi_spis ON ppi_spis.id = temporary_stocks.ppi_spi_id
                               WHERE  temporary_stocks.product_id = {$product_id}  AND temporary_stocks.warehouse_id = {$warehouse_id}
                               AND ppi_spis.project = '{$value->name}';
                            ");

                            $checkTemporary = (collect($checkTemporary))->sum('waiting_stock_out');
                            */
                            $hand = $getProductStockProject-$checkTemporary;
                            $hand = $hand < 0 ? 0 : $hand;
                        @endphp
                        @if($hand > 0)
                        <option
                            value="{{$value->name}}" {{$spi_project == $value->name ? 'selected' : null}}
                            data-warehouse_id="{{ $warehouse_id ?? NULL }}" data-browse="{{$browse}}"
                            data-row_id=" {{$row_id}}"
                            xdata-warehouse_code="{{$warehouse->code}}"
                            data-warehouse_code="{{$warehouse_code}}"
                            data-original_project="{{$original_project}}"
                            data-product_id="{{$product_id}}"
                            data-project_code={{$value->code}}"
                            xdata-landed_project= "{{$landed_project}}"
                           >
                            {{$value->name}} - {{$value->type}}
                            ({{$hand}}) ({{$checkTemporary}})
                        </option>
                        @endif
                    @endforeach
                </select>

            </div>
            <div class="flex-fill text-end">
                <?php /*
                <button class="btn_browse btn my-2 {{$browse == 'Supply' ? 'btn-primary' : 'btn-outline-primary'}}"
                        type="button" data-browse="Supply" data-row_id=" {{$row_id}}"
                        data-warehouse_id="{{$warehouse_id}}" data-warehouse_code={{$warehouse->code}}
                            data-product_id="{{$product_id}}">Supply</button>

                <button class="btn_browse btn my-2 {{$browse == 'Service' ? 'btn-primary' : 'btn-outline-primary'}}"
                        type="button" data-browse="Service" data-row_id=" {{$row_id}}"
                        data-warehouse_id="{{$warehouse_id}}" data-warehouse_code={{$warehouse->code}}
                            data-product_id="{{$product_id}}">Service</button>
 */?>
                <?php /*
                <ul class="nav nav-tabs d-inline-flex">
                    <li class="nav-item">
                        <button type="button" class="nav-link btn_browse d-inline-block   py-1 {{$browse == 'Supply' ? 'active' : ''}}"
                           type="button" data-browse="Supply" data-row_id=" {{$row_id}}"
                           data-warehouse_id="{{$warehouse_id}}" data-warehouse_code={{$warehouse->code}}
                            data-product_id="{{$product_id}}">Supply</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link btn_browse d-inline-block   py-1 {{$browse == 'Service' ? 'active' : ''}}"
                           type="button" data-browse="Service" data-row_id=" {{$row_id}}"
                           data-warehouse_id="{{$warehouse_id}}" data-warehouse_code={{$warehouse->code}}
                            data-product_id="{{$product_id}}">Service</button>
                    </li>
                </ul>
                */ ?>
            </div>
        </div>
        <div class="mt-2">
            <div>
               <small> Own Project: {{$original_project}}</small>
            </div>
            <input type="text" name="" id="searchInput" value="" class="form-control form-control-sm d-block w-100"
                   placeholder="Search by PPI ID, Bundle Size.....">
        </div>
    </div>






    <div class="row mx-0 mytable">
        @php
             $avstock = [];
             $productStockHelper = new \App\Helpers\Warehouse\ProductStockHelper();
             $getProductStock = $productStockHelper->getProductStock($product_id);
             
             $getProductStock = $getProductStock->where('have_stock_status', 'yes')
                                 ->where('project',  $spi_project)
                                 ->where('warehouse_id', $warehouse_id);
             //dd($getProductStock);
        @endphp
        @if(count($getProductStock) > 0)
            @foreach($getProductStock as $key => $item)
            @php
               $makeKey = $key;
               $inputDisabled = $item->bundle_id ? 'disabled' : null;
               $bundle_id = $item->bundle_id ?? null;
               $input_Qty = $item->bundle ? $item->bundle->bundle_size : 1;
               $stockInhand = $item->stock_in_hand;

               $checkTemporaryStock = $productStockHelper->getSpiTemporaryStock($product_id);//->where('warehouse_id', $warehouse_id);
               //dump($warehouse_id);
               $checkTemporaryStock = $bundle_id ? $checkTemporaryStock->whereIn('bundle_id', $bundle_id) : $checkTemporaryStock->whereNull('bundle_id');
               $checkTemporaryStock = $checkTemporaryStock->where('ppi_product_id', $item->ppi_product_id);
               $checkTemporaryStockCount = $checkTemporaryStock->sum('waiting_stock_out');
                /*
                $checkTemporary = \DB::SELECT("
                    SELECT temporary_stocks.*, ppi_spis.project
                        FROM `temporary_stocks`
                        LEFT JOIN ppi_spis ON ppi_spis.id = temporary_stocks.ppi_spi_id
                   WHERE  temporary_stocks.product_id = {$product_id}  AND temporary_stocks.warehouse_id = {$warehouse_id}
                   AND ppi_spis.project = '{$value->name}';
                ");
                $checkTemporaryStockCount = (collect($checkTemporary))->sum('waiting_stock_out');
                */
               $stockInhand = $stockInhand - $checkTemporaryStockCount;
                if($spi_product_id){
                   $checkDispute = $productStockHelper->hasSpiProductDispute($spi_product_id);
//                   $checkDispute = $checkDispute->whereNull('correction_status');
                   //dump($checkDispute);
                   if($checkDispute){
                        $checkDispute = $bundle_id ? $checkDispute->whereIn('bundle_id', $bundle_id) : $checkDispute->whereNull('bundle_id');
                        $checkDispute = $checkDispute->whereIn('ppi_product_id', $item->ppi_product_id);
                        $stockInhand = $stockInhand + $checkDispute->sum('qty');
                        //dump( $checkDispute->sum('qty'));
                   }
               }
//               dump($spi_product_id);
            @endphp
            @if($stockInhand > 0)
            <div class="ekhonKortasi col-lg-12 mb-2 font-11 bw-1 border-gray p-2 shadow-sm tr selectedRowId{{$makeKey}}">
                    <div class="">
                        <span class="td">
                            <span title="ppi_product_id: {{$item->ppi_product_id}} product_id: {{$item->product_id}}">
                                <strong>{{$item->action_format}} ID:</strong>
                                <span class="tdshow">{{$item->ppi_spi_id}} </span>
                                <span class="text-dark fw-bold">.</span>
                            </span>
                            <span>
                                <strong>{{$item->action_format}} Type:</strong> {{$item->ppi_spi_type}}
                                <span class="text-dark fw-bold">.</span>
                            </span>
                            <span>
                                <strong>Project:</strong> <span class="tdshow">{{$item->project}}</span>
                                <span class="text-dark fw-bold">.</span>
                            </span>
                            <span>
                                <strong>Stock in hand:</strong> {{$avstock []=$stockInhand}} <span class="text-dark fw-bold">.</span>
                            </span>
                            <span>
                                <strong>Product State:</strong>
                                <span class="tdshow">  {{$item->product_state }} </span>
                                <span class="text-dark fw-bold">.</span>
                            </span>
                            <span>
                                <strong>Health Status:</strong>
                                <span class="tdshow">{{$item->health_status }} </span>
                                <span class="text-dark fw-bold">.</span>
                            </span>
                            <span>
                                <strong>Unit Price:</strong> {{$item->unit_price}}
                            </span>
                            <br>

                            <!-- If Bundle -->
                            @if($item->bundle)
                                <div class="product_bundle_wrap" title="bundle_id: {{$item->bundle_id}}">
                                         <strong>Bundle Size: </strong>
                                        <span class="tdshow"> {!!  $item->bundle->bundle_size !!}  </span>
                                    </div> <!-- End Bundle -->
                            @endif
                            <div class="crumbswrapper d-inline-block">
                                <div class="crumbs mx-1 my-0 mt-1" id="source_breadcrumb">
                                        <?php foreach($item->sources as $source): ?>
                                    <div class="innerwrap">
                                        <span class="innerItem font-11 ps-2 pe-1 tdshow">
                                            <span>{{$source->source_type}}:</span> {{$source->who_source}}
                                        </span>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </span>
                        <div class="float-end text-end">
                            <label for="">Qty</label>
                            <input {{$inputDisabled}} type="number" id="sing_qty"
                                   class="p-0 d-inline-block border-gray bw-1 h-20 text-center w-25"
                                   data-qty="{{$input_Qty}}"
                                   name=""
                                   value="{{$input_Qty}}"
                                   data-max="{{ $stockInhand }}"
                                   data-min="0">
                            <span class="text-danger">MTR</span>
                            @if($input_Qty > 0)
                                <button type="button" class="btn-dt bg-primary text-white bw-1 bg-primary border-primary"
                                        id="sing_qty_add"
                                        data-bundle_id="{{$bundle_id}}"
                                        data-ppi_product_id="{{$item->ppi_product_id}}"
                                        data-ppi_id="{{$item->ppi_spi_id}}"
                                        data-selected_row="Id{{$makeKey}}"
                                        data-product_qty="{{$input_Qty}}"
                                        data-original_project="{{$original_project}}"
                                        data-landed_project="{{$landed_project}}">
                                    Add
                                </button>
                            @endif
                        </div>
                    </div>
            </div>
            @endif
        @endforeach
        @else
            <span class="alert alert-warning">There is no item</span>
        @endif
        Total : {{array_sum($avstock)}}
    </div>

</div>
<script>
    $('.selectedProductInfoOpenModal  .modal-footer button').click(function(){
        $('.selected-product-modal').empty()
    })
    let inputSingQty = $("input#sing_qty");
    $(inputSingQty).keyup(function () {
        let thisVal = $(this).val()
        let maxVal = $(this).data('max')
        let minVal = $(this).data('min')
        // alert(maxVal)
        if (thisVal > maxVal) {
            alert('You have exceeded the maximum quantity')
            $(this).val(minVal)
        } else if (thisVal < minVal) {
            $(this).val(minVal)
        } else {

        }

    })

    $('button#sing_qty_add').click(function () {
        let selectedRow = $(this).data('selected_row');
        let selectedRowCls = $('.selectedRow' + selectedRow + ' input#sing_qty');
        let qty = selectedRowCls.val();
        //alert(qty);
        //let qty = $(this).data('product_qty');
        $(".colgroup.prb{{$row_id}} input#qty").val(qty)
        let ppiId = $(this).data('ppi_id');
        let ppiProductId = $(this).data('ppi_product_id');
        let fromWarehouse = "{{$warehouse_id}}"
        let bundleID = $(this).data('bundle_id');
        let originalProject =  $(this).data('original_project');
        let LandedProject = $(this).data('landed_project');
        // alert(ppiId)
        let html = '<input type="hidden" value="' + ppiId + '" name="product[{{$row_id}}][ppi_id]" />';
        html += '<input type="hidden" value="' + ppiProductId + '" name="product[{{$row_id}}][ppi_product_id]" />';
        html += '<input type="hidden" value="' + fromWarehouse + '" name="product[{{$row_id}}][from_warehouse]" />';
        html += '<input type="hidden" value="' + LandedProject + '" name="product[{{$row_id}}][landed_project]" />';
        html += '<input type="hidden" value="' + originalProject + '" name="product[{{$row_id}}][originalProject]" />';
        if (bundleID) {
            html += '<input type="hidden" value="' + bundleID + '" name="product[{{$row_id}}][bundle_id]" />';
        }
        $('.ppiInformation{{$row_id}} .ppi_id_append').html(html)
        $('.selectedProductInfoOpenModal').modal('hide');
        toastr.success('Product Qty Added');
    })

    var isLoading = false;

    /** Button Browse Supply/Service Action */
    $('.selectedProductInfoOpenModal').one('click', 'button.btn_browse', function (e) {
        if (!isLoading) {
            isLoading = true;
            $(" #reload_modal").empty();
            $('#selectedProductInfoModalBody').html('Loading...')
            e.preventDefault();
            let dataBrowse = $(this).data('browse');
            let rowId = $(this).data('row_id');
            let productId = $(this).data('product_id');
            let warehouseCode = $(this).data('warehouse_code') ?? "{{request()->get('warehouse_code')}}";
            let thisUrl = "{{route('spi_selected_product_details_info',  ':whcode') }}"
                thisUrl = thisUrl.replace(':whcode', warehouseCode);
            let warehouseId = $(this).data('warehouse_id') ?? "{{request()->get('warehouse_id')}}";
            let spiProject = $(this).data('spi_project');
            let originalProject = $(this).data('original_project');
            //console.log(thisUrl)
            //console.log(dataBrowse)
            //$(this).off('click');
            // $('.selectedProductInfoOpenModal').empty()
            // alert(dataBrowse)
            $.ajax({
                url: thisUrl,
                method: 'GET',
                data: {
                    {{--'_token': "{!! csrf_token() !!}",--}}
                    'browse': dataBrowse,
                    'row_id': rowId,
                    'product_id': productId,
                    'spi_product_id' : "{{$spi_product_id}}",
                    'warehouse_id': warehouseId,
                    'spi_project': spiProject, //'{{$spi_project}}',
                    'original_project': originalProject //'{{$original_project}}'
                },
                success: function (response) {
                    // $('.selectedProductInfoOpenModal #reload_modal').html(response).fadeIn(1000)
                    // $('#reload_modal').empty().html(response)
                    $('#selectedProductInfoModalBody').html(response) // #selectedProductInfoModalBody this id is present in product-modal.blade.php (modalId)
                    isLoading = false;
                }
            })
        }
    })


        /* Select Project */
    $('.selectedProductInfoOpenModal').on('change', 'select.select_a_project', function(e){
        e.preventDefault();
        if (!isLoading) {
            isLoading = true;
            $(" #reload_modal").empty();
            $('#selectedProductInfoModalBody').html('Loading...')
            /*
            let dataBrowse = '{{$browse}}';
            let rowId = '{{$row_id}}';
            let productId = {{$product_id}};
            let warehouseCode = " {{$warehouse_code}}" ?? "{{request()->get('warehouse_code')}}";
            */
            let productId = $(this).find(':selected').data('product_id');
            let dataBrowse = $(this).find(':selected').data('browse');
            let rowId = $(this).find(':selected').data('row_id');
            let warehouseCode = $(this).find(':selected').data('warehouse_code') ?? "{{request()->get('warehouse_code')}}";
            let warehouseId = $(this).find(':selected').data('warehouse_id') ?? "{{request()->get('warehouse_id')}}";

            let thisUrl = '{{route("spi_selected_product_details_info", ":whcode")}}';
                thisUrl = thisUrl.replace(':whcode', warehouseCode);
            {{--let warehouseId = " {{$warehouse_id}}" ?? "{{request()->get('warehouse_id')}}";--}}
            let projects = $(this).find(':selected').val();
            let projectsCode = $(this).find(':selected').data('project_code');
            // /console.log(warehouseCode)

            $.ajax({
                url: thisUrl,
                method: 'GET',
                data: {
                    {{--'_token': "{!! csrf_token() !!}",--}}
                    'browse': dataBrowse,
                    //'spi_type': dataBrowse,
                    'row_id': rowId,
                    'product_id': productId,
                    'warehouse_id': warehouseId,
                    'spi_product_id' : "{{$spi_product_id}}",
                    'spi_project': projects,
                    'landed_project': projects,
                    'original_project': '{{$original_project}}'
                },
                success: function (response) {
                    // $('.selectedProductInfoOpenModal #reload_modal').html(response).fadeIn(1000)
                    // $('#reload_modal').empty().html(response)
                    $('#selectedProductInfoModalBody').html(response)
                    isLoading = false;
                }
            })
        }
    })




    /**
     * Modal Header And Footer Design and Button , Search Box add
     */
    $('.selectedProductInfoOpenModal .modal-header').addClass('d-block py-1 pb-auto').html($('.modal_sub_header_wrap').html())
    $('.selectedProductInfoOpenModal .modal-footer').addClass('py-1 pb-auto')
    $('.modal_sub_header_wrap').empty();

    /** Search Functionality */
    $(document).ready(function () {
        $('#searchInput').keyup(function () {

            // Search text
            var text = $(this).val();

            // Hide all conten  t class element
            $('.tr').hide();

            // Search and show
            // $('.tr:contains("'+text+'")').show();
            // $('.tdshow:contains("'+text+'")').show();
            $('.tr .tdshow').each(function () {
                // console.log($(this).text())
                let chkUpperCase = $(this).text().toUpperCase().indexOf("" + text + "");
                let chkLowerCase = $(this).text().toLowerCase().indexOf("" + text + "");
                if (chkUpperCase != -1 || chkLowerCase != -1) {
                    $('.tdshow:contains("' + text + '")').closest('.tr').show();
                }
            })
        });
    });

    $.expr[":"].contains = $.expr.createPseudo(function (arg) {
        return function (elem) {
            return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
        };
    });
    // $('.select-box').select2();
</script>


<style>
    /* Chrome, Safari, Edge, Opera */
    input#sing_qty::-webkit-outer-spin-button,
    input#sing_qty::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input#sing_qty {
        -moz-appearance: textfield;
    }
</style>


