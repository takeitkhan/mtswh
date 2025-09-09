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
                $warehouses = $Model('Warehouse')::get();
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

                <select name="" id="" class="select_a_project xselect-box">
                    <option value="">Select project</option>
                    @foreach($allProject as $value)
                        <option
                            value="{{$value->name}}" {{$spi_project == $value->name ? 'selected' : null}}
                            data-warehouse_id="{{ $warehouse_id ?? NULL }}" data-browse="{{$browse}}"
                            data-row_id=" {{$row_id}}"
                            xdata-warehouse_code="{{$warehouse->code}}"
                            data-warehouse_code="{{$warehouse_code}}"
                            data-original_project="{{$original_project}}"
                            data-product_id="{{$product_id}}"
                            xdata-landed_project= "{{$landed_project}}"
                           >
                            {{$value->name}} - {{$value->type}}</option>
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

    <div class="row mx-0 xmytable d-none">

        @foreach($checkStock($browse) as $key => $item)

            @php

                 $checkBundle =  $Model('PpiBundleProduct')::where('ppi_id', $item->ppi_spi_id)
                                ->where('ppi_product_id', $item->ppi_spi_product_id)
                                ->where('product_id', $item->product_id)
                                ->get() ?? false;
                  $checkBundle = $checkBundle->isEmpty() ? false : $checkBundle;
                  $alreadyStockOutCount = $Model('ProductStock')::where('action_format', 'Spi')
                                              ->where('product_id', $item->product_id)
                                              ->where('from_ppi_product_id', $item->ppiProduct->id)
                                              ->get('qty')
                                              ->sum('qty');
                  $TotalStockIn = $item->qty;
                  $stockInHand  = $TotalStockIn - $alreadyStockOutCount;


                  $input_Qty = $stockInHand > 0 ? 1 : 0;
                  $makeKey = 'bw'.$key;
                  //dump($alreadyStockOutCount);

            @endphp

           @if($stockInHand > 0)
                <div class="col-lg-12 mb-2 font-11 bw-1 border-gray p-2 shadow-sm tr xselectedRowId{{$makeKey}}">
                    <div class="">
                            <span class="td">

                                <span title="ppi_product_id: {{$item->ppiProduct->id}} product_id: {{$item->productInfo->id}}">
                                    <strong>{{$item->action_format}} ID:</strong> <span
                                        class="tdshow">{{$item->ppi_spi_id}} </span> <span class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>{{$item->action_format}} Type:</strong> {{$item->ppiSpi->ppi_spi_type}} <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Project:</strong> <span class="tdshow">{{$item->ppiSpi->project}}</span> <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Stock in hand:</strong> {{ $stockInHand}} <span class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Product State:</strong>  <span
                                        class="tdshow">  {{$item->ppiProduct->product_state }} </span> <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Health Status:</strong> <span
                                        class="tdshow">{{$item->ppiProduct->health_status }} </span> <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Unit Price:</strong> {{$item->ppiProduct->unit_price}}
                                </span>
                                <br>
                                <!-- If Bundle -->

                                <div class="crumbswrapper d-inline-block">
                                    <div class="crumbs mx-1 my-0 mt-1" id="source_breadcrumb">
                                        <?php foreach($item->source as $source): ?>
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
                            <input type="number" id="sing_qty"
                                   class="p-0 d-inline-block border-gray bw-1 h-20 text-center w-25"
                                   data-qty="{{$input_Qty}}"
                                   name=""
                                   value="{{$input_Qty}}"
                                   data-max="{{ $stockInHand }}"
                                   data-min="0"/>
                            <span class="text-danger">MTR</span>
                            @if($input_Qty > 0)
                                <button type="button" class="btn-dt bg-primary text-white bw-1 bg-primary border-primary"
                                        id="sing_qty_add"
                                        data-bundle_id="{{$bundle_id ?? null}}"
                                        data-ppi_product_id="{{$item->ppi_spi_product_id}}"
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

    </div>































    <div class="row mx-0 myTable">
        @if(count($checkStock($browse)) > 0)
        @foreach($checkStock($browse) as $key =>  $item)
{{--                    @dump($item)--}}
            @php
                $checkBundle =  $Model('PpiBundleProduct')::where('ppi_id', $item->ppi_spi_id)
                                    ->where('ppi_product_id', $item->ppi_spi_product_id)
                                    ->where('product_id', $item->product_id)
                                    ->get();
                //dump($checkBundle)

                $produnctTemplate = function($bundleKey = null, $bundle = null) use ($key, $item, $Model, $warehouse_id, $spi_product_id, $original_project, $landed_project) {
                        $makeKey = $bundleKey > 0 ? 'b'.$bundleKey : 'bw'.$key;
                //dump($makeKey);

                if($bundle){
                    /*
                     $stockIn = $Model('ProductStock')::checkStock($item->product_id, 'Ppi', 'In', [
                         'warehouse_id' => $warehouse_id,
                         'bundle_id' => $bundle->id,
                         'ppi_spi_id' => $item->ppi_spi_id,
                         'ppi_spi_product_id' => $item->ppi_spi_product_id,
                         ]);
                    $stockOut = $Model('ProductStock')::checkStock($item->product_id, 'Spi', 'Out', ['warehouse_id' => $warehouse_id, 'bundle_id' => $bundle->id]);
                    //$stockInhand = $stockIn - $stockOut;
                    $stockInhand = $stockIn;
                      */
                    //New
                      $alreadyStockOutCount = $Model('ProductStock')::where('action_format', 'Spi')
                                                  ->where('product_id', $item->product_id)
                                                  ->where('from_ppi_product_id', $item->ppiProduct->id)
                                                  ->get('qty')
                                                  ->sum('qty');

                      $TotalStockIn = $Model('ProductStock')::where('action_format', 'Ppi')
                                      ->where('product_id', $item->product_id)
                                      ->where('ppi_spi_product_id', $item->ppi_spi_product_id)
                                      ->where('bundle_id', $bundle->id)
                                      ->get('qty')
                                      ->sum('qty');
                      $stockInhand  = $TotalStockIn; //- $alreadyStockOutCount;
                      //dump($stockInhand);
                    //New

                    $unit_price = $bundle->bundle_price;
                    $input_Qty = $stockInhand == 0 ? '0' : $bundle->bundle_size;
                    $inputDisabled = 'disabled';
                    $bundle_id = $bundle->id;
                    //dump($item->product_id);

                    $checkWaitListForStockOut = $Model('TemporaryStock')::leftjoin('spi_products', 'spi_products.id', 'temporary_stocks.spi_product_id')
                                                ->where('temporary_stocks.product_id', $item->product_id)
                                                ->where('spi_products.bundle_id', '!=', Null)
                                                ->where('temporary_stocks.action_format', 'Spi')
                                                ->get()->sum('waiting_stock_out');

                    $checkWaitListForStockOut = !empty($checkWaitListForStockOut) ? $checkWaitListForStockOut: 0;

                    //dump($bundle_id);

                    $stockInhand = $stockInhand - $checkWaitListForStockOut;

                } else {
                    /*
                     $stockIn = $Model('ProductStock')::checkStock($item->product_id, 'Ppi', 'In', [
                        'warehouse_id' => $warehouse_id,
                        'ppi_spi_id' => $item->ppi_spi_id,
                        //'ppi_spi_product_id' => $item->ppi_spi_product_id,
                        ]);
                    $stockOut = $Model('ProductStock')::checkStock($item->product_id, 'Spi', 'Out', ['warehouse_id' => $warehouse_id]);
                    //$stockInhand = $stockIn - $stockOut;
                    $stockInhand = $stockIn;
                       */


                    //new

                      $alreadyStockOutCount = $Model('ProductStock')::where('action_format', 'Spi')
                                                  ->where('product_id', $item->product_id)
                                                  ->where('from_ppi_product_id', $item->ppiProduct->id)
                                                  ->get('qty')
                                                  ->sum('qty');
                      $TotalStockIn = $item->qty;
                      $stockInhand  = $TotalStockIn - $alreadyStockOutCount;
                      //dump($stockInhand);
                    //new
                     $unit_price = $item->ppiProduct->unit_price;
                     $input_Qty = $stockInhand == 0 ? 0 : 1;
                     $inputDisabled = null;
                     $bundle_id = null;

                //dump($stockIn);
                /*
                $checkWaitListForStockOut = $Model('SpiProduct')::leftjoin('ppi_spi_statuses as sts', 'spi_products.id', 'sts.ppi_spi_product_id')
                                           ->select('spi_products.qty')
                                           ->where('spi_products.from_warehouse', $item->warehouse_id)
                                           ->where('sts.status_for', 'Spi')
                                           ->where('spi_products.product_id', $item->product_id)
                                           ->where('spi_products.ppi_product_id', $item->ppi_spi_product_id)
                                           ->where('spi_products.bundle_id', $bundle_id)
                                           ->whereNotIn('sts.code', ['spi_product_out_from_stock'])
                                           ->get()->groupBy('ppi_spi_product_id');
                */

                $checkWaitListForStockOut = $Model('TemporaryStock')::leftjoin('spi_products', 'spi_products.id', 'temporary_stocks.spi_product_id')
                                                ->where('temporary_stocks.product_id', $item->product_id)
                                                ->whereNull('spi_products.bundle_id')
                                                ->where('temporary_stocks.action_format', 'Spi')
                                                ->where('temporary_stocks.spi_product_id', $spi_product_id)
                                                ->get()->sum('waiting_stock_out');

                $checkWaitListForStockOut = !empty($checkWaitListForStockOut) ? $checkWaitListForStockOut: 0;



                $stockInhand = $stockInhand - $checkWaitListForStockOut;

                //dump($checkWaitListForStockOut);


                //check if Dispute
                $checkDispute = $Model('PpiSpiDispute')::where('status_for', 'Spi')->where('ppi_spi_product_id', $spi_product_id)
                                ->where('action_format', 'Dispute')->first();
                $checkCorrection = false;
                //dump($checkDispute);
                if($checkDispute){
                    $checkCorrection = $Model('PpiSpiDispute')::where('status_for', 'Spi')
                                ->where('ppi_spi_product_id', $spi_product_id)
                                ->where('correction_dispute_id', $checkDispute->id)
                                ->where('action_format', 'Correction')->first() ?? false;

                    if($checkCorrection == false){
                        //$checkWaitListForStockOut = $checkWaitListForStockOut
                        $preventQty = $Model('SpiProduct')::where('id',  $spi_product_id)->first()->qty ?? 0;
                        $stockInhand = $stockInhand + $preventQty;
                        //dump($preventQty);
                    }
                  }
                //end
            }
            $input_Qty = $stockInhand == 0 ? 0 : $input_Qty;
            //dump($bundle_id);
             //dump($input_Qty);
            @endphp
                @if($stockInhand > 0)
                    <div class="col-lg-12 mb-2 font-11 bw-1 border-gray p-2 shadow-sm tr selectedRowId{{$makeKey}}">
                        <div class="">
                            <span class="td">

                                <span title="ppi_product_id: {{$item->ppiProduct->id}} product_id: {{$item->productInfo->id}}">
                                    <strong>{{$item->action_format}} ID:</strong> <span
                                        class="tdshow">{{$item->ppi_spi_id}} </span> <span class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>{{$item->action_format}} Type:</strong> {{$item->ppiSpi->ppi_spi_type}} <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Project:</strong> <span class="tdshow">{{$item->ppiSpi->project}}</span> <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Stock in hand:</strong> {{$stockInhand}} <span class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Product State:</strong>  <span
                                        class="tdshow">  {{$item->ppiProduct->product_state }} </span> <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Health Status:</strong> <span
                                        class="tdshow">{{$item->ppiProduct->health_status }} </span> <span
                                        class="text-dark fw-bold">.</span>
                                </span>
                                <span>
                                    <strong>Unit Price:</strong> {{$unit_price}}
                                </span>
                                <br>
                                <!-- If Bundle -->
                                @if($bundle)
                                    <div class="product_bundle_wrap" title="bundle_id: {{$bundle_id}}">
                                         <strong>Bundle Size: </strong>
                                        <span class="tdshow"> {!!  $bundle->bundle_size !!}  </span>
                                    </div> <!-- End Bundle -->
                                @endif
                                <div class="crumbswrapper d-inline-block">
                                    <div class="crumbs mx-1 my-0 mt-1" id="source_breadcrumb">
                                        <?php foreach($item->source as $source): ?>
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
                                            data-ppi_product_id="{{$item->ppi_spi_product_id}}"
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
            <?php } ?>

            @if(count($checkBundle) > 0)
                @foreach($checkBundle as $bundleKey => $bundle)
                    {!! $produnctTemplate($bundle->id, $bundle) !!}
                @endforeach
            @else
                {!! $produnctTemplate() !!}
            @endif
        @endforeach
        @else
            <span class="alert alert-warning">There is no items</span>
        @endif

    </div>


</div>
<script>
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


