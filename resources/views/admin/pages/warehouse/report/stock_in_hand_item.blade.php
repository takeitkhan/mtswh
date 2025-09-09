<h4>Stock In hand</h4>
@php
    $productStockHelper = new \App\Helpers\Warehouse\ProductStockHelper();
    $getProductStock = $productStockHelper->getProductStock($product->id);
    $getProductStock = $getProductStock->where('have_stock_status', 'yes');
//    dump($getProductStock);
@endphp
@foreach($getProductStock as $key => $item)
    @php
        $makeKey = $key;
        $inputDisabled = $item->bundle_id ? 'disabled' : null;
        $bundle_id = $item->bundle_id ?? null;
        $input_Qty = $item->bundle ? $item->bundle->bundle_size : 1;
        $stockInhand = $item->stock_in_hand;

        $checkTemporaryStock = $productStockHelper->getSpiTemporaryStock($product->id);//->where('warehouse_id', $warehouse_id);
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
        /*
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
        //dump($spi_product_id);
        */
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

            </div>
        </div>
    @endif
@endforeach
