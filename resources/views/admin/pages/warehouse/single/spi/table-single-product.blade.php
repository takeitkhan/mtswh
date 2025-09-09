<div class="d-none">
    <form></form>
</div>
@php
    global $singleProductValidationComplete;
    global $singleProductValidationTotal;
    global $singleProductValidationDone;
@endphp
<!----- th => ppi-product.blade.php -->

@foreach ($getSpiProduct as $product)
    @php $checkProductIsSet = $Model('PpiSetProduct')::getSet($product->id); @endphp
    @if(count($checkProductIsSet) > 0)

    @else
        <tr class="pr_row_{{$product->id}} {{$product->any_warning_cls}}">
            <td class="not_print">
                @php
                    $thisProductDisputeCorrrection = $Model('PpiSpiDispute')::disputeCorrectionDone('Spi', $spi->id, $product->id, ['action_performed_by' => auth()->user()->id, 'route_permission' => 'spi_product_info_correction_by_boss_action']);

                    if($thisProductDisputeCorrrection == true){
                        if($thisProductDisputeCorrrection == 'true'){
                            $editClass =  'done_this_action_btn';
                        }elseif($thisProductDisputeCorrrection == 'correction-not-done') {
                            //var_dump($thisProductDisputeCorrrection);
                            $editClass =  null;
                        }
                    }else{
                        $editClass =  'done_this_action_btn';
                    }
                @endphp

                <span
                    class="done_this_action_btn"> {!! $ButtonSet::delete('spi_product_destroy', [$warehouse_code, $product->id]) !!} </span>
                <span
                    class="{{$editClass ?? null}} spiProductEditBtn"> {!! $ButtonSet::edit('spi_product_edit', [$warehouse_code, $product->id]) !!}</span>

            </td>

        @php
            $disputeData =  $Model('PpiSpiDispute')::disputeData('Spi', $spi->id, $product->id);

            $checkThisDisputes = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id, [
                                'ppi_spi_product_id' => $product->id,
                                'code' => 'spi_dispute_by_wh_manager',
                                'status_format' => 'Main'
                                ]);
            $checkEditAfterDispute = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id, [
                                    'ppi_spi_product_id' => $product->id,
                                    'code' => 'spi_product_edited'
                                ]);

        @endphp
        <!-- Dispute Chcekbox -->

            <td class="not_print">

                <!-- Correction Button -->

            @if(isset($correctionRoute))

                @if($disputeData && !empty($checkEditAfterDispute) && !empty($checkThisDisputes) && $checkEditAfterDispute->id > $checkThisDisputes->id)
                    <!--show  If dispute  correction done -->
                        @if($coorectionData = $Model('PpiSpiDispute')::checkDisputeCorrection('Spi', $disputeData->id))
                            <i class="fa fa-check-circle m-0 h3 w-auto text-success" style="font-size: 20px;"></i>
                        @else
                        <!-- show if dispute -->
                            @php
                                $html= '<label style="cursor:pointer" for="'.$product->id.'">Confirm To Correction</label> <input id="'.$product->id.'" class="d-none"  type="radio"
                                name="correction_ele"
                                value="'.$Model('PpiSpiDispute')::disputeData('Spi', $spi->id, $product->id)->id.'"/>';
                            @endphp
                            <button type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#correctionButton"
                                    data-url="{{$correctionRoute}}"
                                    xname="correction_ele" id="correction_button"
                                    style="cursor: none"
                                    class="btn btn-sm btn-orange text-white p-0"> {!! $html !!}
                            </button>

                            {!!
                                $Component::confirmModal('correctionButton', 'form#tbl_ppi_product_form_action', 'Are you sure ?', '', '')
                            !!}
                        @endif
                    @endif

        @endif
            </td>
        <!-- End checkbox -->


            <!-- product Name -->
            <td title="product-id={{$product->product_id}} spi_product_id={{$product->spi_product_id}} ppi_product_id={{$product->ppi_product_id}} ppi_id={{$product->ppi_id}}"
                class="product {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'product')) ? 'text-danger fw-bold' : '' }}">
                {!! $product->product_name !!}
            </td>
            <!-- Qty -->
            <td class="qty p-1 {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'qty')) ? 'text-danger fw-bold' : '' }}">

                <table>
                    <thead>
                    <tr>
                        @if($product->bundle_id)
                            <th title="Bundle Size " class="bundle-row text-center">Size</th>
                        @else
                            <th title="Qty" class="bundle-row text-center">Qty</th>
                        @endif
                        <th title="Unit Price" class="bundle-row text-center ppi_product_price_show">Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td
                            @if($product->bundle_id)
                                title="bundle_id: {{$product->bundle_id}}"
                            @endif
                            class="bundle-row text-center">{!! $product->qty !!}</td>
                        <td class="bundle-row text-center ppi_product_price_show">{!! $product->unit_price !!}</td>
                    </tr>
                    </tbody>
                </table>

            </td>
            <!-- unit -->
            <td class="unit">
                @if($product->product_state == 'Cut-Piece')
                    Bundle
                @else
                    {!! $Model('AttributeValue')::getValueById($product->product_unit_id)  !!}
                @endif
            </td>

            <!-- Price -->
            <td class="not_print price ppi_product_price_show {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'price')) ? 'text-danger fw-bold' : '' }}">
                {!! $product->price !!}
            </td>

            <td>  {!! $productState =  $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state']) !!} </td>
            <td>  {!! $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'health_status']) !!} </td>
            <td> {!! $product->barcode_format !!} </td>
            <td class="not_print"> {!! $product->note !!} </td>
            <td>
                {{ ($product->from_warehouse != $product->warehouse_id) ? 'Lended' : 'Regular' }} <br>
                From {{$Model('Warehouse')::name($product->from_warehouse)}}
            </td>
            <td class="not_print">
                @if($disputeData)
                    <span class="alert-danger">
                        <span class="text-danger">{!! $disputeData->note ?? Null !!}
                        By {{$Model('User')::getColumn($disputeData->action_performed_by, 'name')}}
                        </span>
                    </span>
                    @if($coorectionData = $Model('PpiSpiDispute')::checkDisputeCorrection('Spi', $disputeData->id))
                        <br>
                        <span class="alert-success">
                        <span
                            class="class">Correction by {{$Model('User')::getColumn($coorectionData->action_performed_by, 'name')}}</span>
                    </span>
                    @endif
                @endif
            </td>
            <td class="text-center not_print">
                @php
                    $checkStockOutThisProduct = false;
                @endphp
                @if(auth()->user()->hasRoutePermission('spi_get_line_item'))
                    <?php if ($productState == 'Cut-Piece') {
                        $bundleName = $product->bundle_id;
                        $addBundleGetMethod = '?bundle=' . $bundleName;
                    } else {
                        $addBundleGetMethod = null;
                    }

                    //$ppiLastPpiProductStatus = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id, ['ppi_spi_product_id' => $product->spi_product_id]);
                    //$ppiLastPpiProductStatusCode = $ppiLastPpiProductStatus->code ?? null;
                    //dump($ppiLastPpiProductStatusCode);
                    $checkStockOutThisProduct = $Model('PpiSpiStatus')::checkSpiStatus($spi->id, 'spi_product_out_from_stock', ['ppi_spi_product_id' => $product->spi_product_id]);

                    if ($checkStockOutThisProduct) {
                        $validationBgColor = 'green';
                        $validationText = 'Validated';
                        $singleProductValidationComplete += 1;
                    } else {
                        $validationBgColor = 'blue';
                        $validationText = 'Vailidation';
                    }
                    $singleProductValidationTotal += 1;

                    if(auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action')){
                        $validationText = $validationText;
                    }else {
                        $validationText = 'Details';
                    }

                    ?>

                    <a class="btn btn-sm  py-0 btn-soft-{{$validationBgColor}}-gradient"
                       href="{{ route('spi_get_line_item', [$warehouse_code  , $product->id]) }}{{$addBundleGetMethod ?? null}}">
                        <i style="font-size: 17px;"
                           class="fas fa-barcode bg-transparent  m-auto d-inline-block"></i> {{$validationText}}
                    </a>
                @endif

                @if($checkStockOutThisProduct)
                    <p class="badge bg-success mt-2">Stocked out</p>
                @endif
            </td>
        </tr>
    @endif
@endforeach



@php
    if($singleProductValidationTotal == $singleProductValidationComplete){
        $singleProductValidationDone = true;
    }
@endphp


@section('cusjs')
    @parent
    <style>
        .bundle-row {
            font-size: 10px !important;
            padding: 0px 1px !important;
            width: 35px !important;
        }
    </style>
@endsection
