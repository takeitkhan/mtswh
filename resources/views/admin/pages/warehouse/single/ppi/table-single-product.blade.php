<div class="d-none"><form></form></div>
@php
   global $singleProductValidationComplete;
   global $singleProductValidationTotal;
   global $singleProductValidationDone;
@endphp
<!----- th => ppi-product.blade.php -->

@foreach ($getPpiProduct as $product)
    @php $checkProductIsSet = $Model('PpiSetProduct')::getSet($product->id); @endphp
    @if(count($checkProductIsSet) > 0)

    @else
        <tr class="pr_row_{{$product->id}}">
            <td id="not_print" class="not_print">
                @php
                    $thisProductDisputeCorrrection = $Model('PpiSpiDispute')::disputeCorrectionDone('Ppi', $ppi->id, $product->id, ['action_performed_by' => auth()->user()->id, 'route_permission' => 'ppi_product_info_correction_by_boss_action']);

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

                <span class="done_this_action_btn">{!! $ButtonSet::delete('ppi_product_destroy', [$warehouse_code, $product->id]) !!}</span>
                <span class="{{$editClass ?? null}} ppiProductEditBtn"> {!! $ButtonSet::edit('ppi_product_edit', [$warehouse_code, $product->id]) !!}</span>
            </td>
            <td id="not_print" class="xform-check text-center ppi_set_product_add not_print">
                @if(auth()->user()->hasRoutePermission('ppi_set_product_add'))
                    @if(count($getPpiProduct) > 0)
                        <span class="done_this_action">
                            @if($product->product_state == 'Cut-Piece')
                            @else
                                <input type="checkbox" class="checkItem mb-0" name="for_create_set[]" value="{{$product->id}}" id="for_create_set">
                            @endif
                        <span>
                    @endif
                @endif
            </td>
            @php
                $disputeData =  $Model('PpiSpiDispute')::disputeData('Ppi', $ppi->id, $product->id);
                $checkThisDisputes = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id, [
                                    'ppi_spi_product_id' => $product->id,
                                    'code' => 'ppi_dispute_by_wh_manager',
                                    'status_format' => 'Main'
                                    ]);
                $checkEditAfterDispute = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id, [
                            'ppi_spi_product_id' => $product->id,
                            'code' => 'ppi_product_edited'
                            ]);

            @endphp
            <!-- Dispute Chcekbox -->
            <td class="not_print">
            <!-- Correction Button -->
            @if(isset($correctionRoute))

{{--                    @if($disputeData && !empty($checkEditAfterDispute) && $checkEditAfterDispute->code == 'ppi_product_edited')--}}
                    @if($disputeData && !empty($checkEditAfterDispute) && !empty($checkThisDisputes) && $checkEditAfterDispute->id > $checkThisDisputes->id)
                        <!--show  If dispute  correction done -->
                        @if($coorectionData = $Model('PpiSpiDispute')::checkDisputeCorrection('Ppi', $disputeData->id))
                            <i class="fa fa-check-circle m-0 h3 w-auto text-success" style="font-size: 20px;"></i>
                        @else
                        <!-- show if dispute -->
                            @php
                                    $html= '<label style="cursor:pointer"  for="'.$product->id.'">Confirm To Correction</label> <input id="'.$product->id.'" class="d-none"  type="radio"
                                    name="correction_ele"
                                    value="'.$Model('PpiSpiDispute')::disputeData('Ppi', $ppi->id, $product->id)->id.'"/>';
                            @endphp
                            <button type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#correctionButton"
                                    data-url = "{{$correctionRoute}}"
                                    style="cursor: none"
                                    xname="correction_ele" id="correction_button" class="btn btn-sm btn-orange py-0"> {!! $html !!}
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
            <td title="product-id={{$product->product_id}} ppi_product_id={{$product->ppi_product_id}}"
            class="product {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Ppi', $ppi->id, $product->id, 'product')) ? 'text-danger fw-bold' : '' }}">
                {!! $product->product_name !!}
            </td>
            <!-- Qty -->
            <td class="qty p-1 {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Ppi', $ppi->id, $product->id, 'qty')) ? 'text-danger fw-bold' : '' }}">
                @if($product->product_state == 'Cut-Piece')
                    <!-- Bundle product -->

                    <table>
                        <thead>
                            <tr>
                                <th title="Bundle Size" class="bundle-row text-center">Size</th>
                                <th title="Bundle Unit Price" class="bundle-row text-center ppi_product_price_show">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $bundles = $Model('PpiBundleProduct')
                                            ::where('ppi_product_id', $product->id)
                                            ->get();
                                $bundleName = [];
                                $totalBundle = [];
                            @endphp
                            @foreach( $bundles as $bundle)
                            <tr>
                                <td style="color:#333" class="bundle-row text-center" title="bundle_id: {{$bundle->id}}">
                                    {!! $totalBundle []= $bundle->bundle_size !!} {!! $Query::accessModel('AttributeValue')::getValueById($product->product_unit_id)  !!}
                                </td>
                                <td style="color:#333" class="bundle-row text-center ppi_product_price_show"> {!! $bundle->bundle_price !!}</td>
                            <tr>
                                <td class="bundle-row text-center ppi_product_price_show" colspan="3">Total:  {!! $bundle->bundle_size*$bundle->bundle_price !!}</td>
                            </tr>
                            @endforeach
                            @php $bundleName = 'true'  @endphp
                        </tbody>
                    </table>

                    <!-- End Bundle Product -->
                @else
                    <table>
                        <thead>
                            <tr>
                                <th title="Qty" class="bundle-row text-center">Qty</th>
                                <th title="Unit Price" class="bundle-row text-center ppi_product_price_show">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="bundle-row text-center">{!! $product->qty !!}</td>
                                <td class="bundle-row text-center ppi_product_price_show">{!! $product->unit_price !!}</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
                </td>
                <!-- unit -->
                <td class="unit">
                    @if($product->product_state == 'Cut-Piece')
                        Bundle <br>
                        Total: {{array_sum($totalBundle) ?? null}} {!! $Query::accessModel('AttributeValue')::getValueById($product->product_unit_id)  !!}
                    @else
                    {!! $Model('AttributeValue')::getValueById($product->product_unit_id)  !!}
                    @endif
                </td>

                <!-- Price -->
                <td class="not_print price ppi_product_price_show {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Ppi', $ppi->id, $product->id, 'price')) ? 'text-danger fw-bold' : '' }}">
                    {!! $product->price !!}
                </td>

            <td> {!! $product->product_state !!} </td>
            <td> {!! $product->health_status !!} </td>
            <td> {!! $product->barcode_format !!} </td>
            <td class="not_print"> {!! $product->note !!} </td>
            <td class="not_print">
                @if($disputeData)
                    <span class="alert-danger">
                        <span class="text-danger">{!! $disputeData->note ?? Null !!}
                        By {{$Model('User')::getColumn($disputeData->action_performed_by, 'name')}}
                        </span>
                    </span>
                    @if($coorectionData = $Model('PpiSpiDispute')::checkDisputeCorrection('Ppi', $disputeData->id))
                    <br>
                    <span class="alert-success">
                        <span class="class">Correction by {{$Model('User')::getColumn($coorectionData->action_performed_by, 'name')}}</span>
                    </span>
                    @endif
                @endif
            </td>
                <td class="text-center not_print">
                    <?php if($product->product_state == 'Cut-Piece'){
                        $addBundleGetMethod = '?bundle='.$bundleName;
                    }else {
                        $addBundleGetMethod = null;
                    }

                    //$ppiLastPpiProductStatus = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id, ['ppi_spi_product_id' => $product->ppi_product_id]);
                    //$ppiLastPpiProductStatusCode = $ppiLastPpiProductStatus->code ?? null;
                    $checkStockInThisProduct = $Model('PpiSpiStatus')::checkPpiStatus($ppi->id, 'ppi_new_product_added_to_stock', ['ppi_spi_product_id' => $product->ppi_product_id]);

                    //if($ppiLastPpiProductStatusCode === 'ppi_new_product_added_to_stock'){
                    if($checkStockInThisProduct){
                        $validationBgColor = 'green';
                        $validationText = 'Validated';
                        $singleProductValidationComplete += 1;
                    } else {
                        $validationBgColor = 'blue';
                        $validationText = 'Vailidation';
                    }
                    $singleProductValidationTotal += 1;

                    if(auth()->user()->hasRoutePermission('ppi_dispute_by_wh_manager_action')){
                        $validationText = $validationText;
                    }else {
                        $validationText = 'Details';
                    }
                    ?>
                @if(auth()->user()->hasRoutePermission('ppi_get_line_item'))
                    <a class="btn btn-sm  py-0 btn-soft-{{$validationBgColor}}-gradient" href="{{ route('ppi_get_line_item', [$warehouse_code  , $product->id]) }}{{$addBundleGetMethod ?? null}}">
                        <i style="font-size: 17px;" class="fas fa-barcode bg-transparent  m-auto d-inline-block"></i>  {{$validationText}}
                    </a>
                @endif
                @php

                @endphp
                @if($checkStockInThisProduct)
                <p class="badge bg-success mt-2">Stocked in</p>
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
        .bundle-row{
            font-size: 10px !important;
            padding: 0px 1px !important;
            width: 35px !important;
        }
    </style>
@endsection
