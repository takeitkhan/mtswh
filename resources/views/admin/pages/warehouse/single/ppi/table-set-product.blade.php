<div class="d-none">
    <form></form>
</div>
@php
    global $setProductValidationComplete;
    global $setProductValidationTotal;
    global $setProductValidationDone;

@endphp
@php $getSet = $Model('PpiSetProduct')::getSetByPpi($ppi->id); @endphp
@foreach ($getSet as $data)
    <tbody style="border: 3px solid #e1d36d">
    <!-- Set Name -->
    <tr>
        <td colspan="13" class="text-center alert-warning">
            <b title="set id ={{$data->id}}">{{$data->set_name}} Start</b>
            <span class="done_this_action_btn">
                    {!! $ButtonSet::delete('ppi_set_product_destroy', [$warehouse_code, $data->id], ['class' => 'setDelete', 'id' => 'setDelete'.$data->id]) !!}
                </span>
        </td>
    </tr>

    @php
        $getPpiProductIds = explode(',', $data->ppi_product_id);
    @endphp
    @foreach( $getPpiProductIds as $getPpiProductId)
        @php
            $product = $Model('PpiProduct')::ppiProductInfoByPpiProductId($getPpiProductId);
            //dump($getPpiProductId)
        @endphp
        @if(!empty($product))
            <tr>
                <td>
                    @if(auth()->user()->hasRoutePermission('ppi_product_destroy_from_set'))
                        <span class="done_this_action_btn">
                            @php
                                //$routeProductDeleteFromSet = ;
                                echo $ButtonSet::delete('ppi_product_destroy_from_set', [request()->get('warehouse_code'), $data->id, $getPpiProductId], ['id' => 'setp'.$getPpiProductId]);
                            @endphp
                            </span>
                    @endif

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
                    <span
                        class="{{$editClass ?? null}} ppiProductEditBtn"> {!! $ButtonSet::edit('ppi_product_edit', [$warehouse_code, $product->id]) !!}</span>
                </td>
                <td class="ppi_set_product_add"></td>
            @php
                $disputeData =  $Model('PpiSpiDispute')::disputeData('Ppi', $ppi->id, $product->id);
                $checkThisDisputes = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id, [
                                                'ppi_spi_product_id' => $product->id,
                                                'code' => 'ppi_dispute_by_wh_manager',
                                                 'status_format' => 'Main'
                                                ]);
                $checkEditAfterDispute = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id, ['ppi_spi_product_id' => $product->id, 'code' => 'ppi_product_edited']);
            @endphp
            <!-- Discpute / correction -->
                <td>

                @if(isset($correctionRoute))
                    @if($disputeData && !empty($checkEditAfterDispute) && !empty($checkThisDisputes) && $checkEditAfterDispute->id > $checkThisDisputes->id)
                        <!--show  If dispute  correction done -->
                            @if($coorectionData = $Model('PpiSpiDispute')::checkDisputeCorrection('Ppi', $disputeData->id))
                                <i class="fa fa-check-circle m-0 h3 w-auto text-success" style="font-size: 20px;"></i>
                            @else
                            <!-- show if dispute -->
                                @php
                                    $html= '<label style="cursor:pointer" for="'.$product->id.'">Confirm To Correction</label> <input id="'.$product->id.'" class="d-none"  type="radio"
                                    name="correction_ele"
                                    value="'.$Model('PpiSpiDispute')::disputeData('Ppi', $ppi->id, $product->id)->id.'"/>';
                                @endphp
                                <button type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#correctionButton"
                                        data-url="{{$correctionRoute}}"
                                        xname="correction_ele"
                                        style="cursor: none"
                                        id="correction_button"
                                        class="btn btn-sm btn-orange py-0"> {!! $html !!}
                                </button>

                                {!!
                                    $Component::confirmModal('correctionButton', 'form#tbl_ppi_product_form_action', 'Are you sure ?', '', '')
                                !!}
                            @endif
                        @endif
                </td>
            @endif
            <!-- End checkbox -->

                </td>

                <td class="text-dark"
                    title="product-id={{$product->product_id}} ppi_product_id={{$product->ppi_product_id}}">
                    {!! $product->product_name !!}
                </td>
                <!-- Qty -->
                <td class="text-dark">
                    <table>
                        <thead>
                        <tr>
                            <th title="Bundle Size" class="bundle-row text-center">Qty</th>
                            <th title="Bundle Unit Price" class="bundle-row text-center ppi_product_price_show">Price
                            </th>
                        </tr>
                        </thead>
                        <tbpody>
                            <tr>
                                <td class="bundle-row text-center">{!! $product->qty !!}</td>
                                <td class="bundle-row text-center ppi_product_price_show">{!! $product->unit_price !!}</td>
                            </tr>
                        </tbpody>
                    </table>

                </td>
                <!--- Qty -->
                <td>
                    {!! $Model('AttributeValue')::getValueById($product->product_unit_id)  !!}
                </td>
                <td class="text-dark ppi_product_price_show"> {!! $product->price !!} </td>

                <td class="text-dark"> {!! $product->product_state !!} </td>
                <td class="text-dark"> {!! $product->health_status !!} </td>
                <td class="text-dark"> {!! $product->barcode_format !!} </td>
                <td class="text-dark"> {!! $product->note !!} </td>
                <td>
                    @if($disputeData)
                        <span class="alert-danger">
                                <span class="text-danger">{!! $disputeData->note ?? Null !!}
                                By {{$Model('User')::getColumn($disputeData->action_performed_by, 'name')}}
                                </span>
                            </span>
                        @if($coorectionData = $Model('PpiSpiDispute')::checkDisputeCorrection('Ppi', $disputeData->id))
                            <br>
                            <span class="alert-success">
                                <span
                                    class="class">Correction by {{$Model('User')::getColumn($coorectionData->action_performed_by, 'name')}}</span>
                            </span>
                        @endif
                    @endif
                </td>

                <td>
                    @php
                        //$ppiLastPpiProductStatus = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id, ['ppi_spi_product_id' => $product->ppi_product_id]);
                        //$ppiLastPpiProductStatusCode = $ppiLastPpiProductStatus->code ?? null;

                          $checkStockInThisProduct = $Model('PpiSpiStatus')::checkPpiStatus($ppi->id, 'ppi_new_product_added_to_stock', ['ppi_spi_product_id' => $product->ppi_product_id]);

                         if($checkStockInThisProduct){
                             $validationBgColor = 'green';
                             $validationText = 'Validated';
                             $setProductValidationComplete += 1;
                         } else {
                             $validationBgColor = 'blue';
                             $validationText = 'Vailidation';
                         }
                         $setProductValidationTotal += 1;

                        if(auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action')){
                            $validationText = $validationText;
                        }else {
                            $validationText = 'Details';
                        }
                    @endphp

                    @if(auth()->user()->hasRoutePermission('ppi_get_line_item'))
                        <a class="btn btn-sm py-0 btn-soft-{{$validationBgColor}}-gradient"
                           href="{{ route('ppi_get_line_item', [$warehouse_code  , $product->id]) }}?set-name={{$data->set_name}}">
                            <i style="font-size: 17px;"
                               class="fas fa-barcode bg-transparent  m-auto d-inline-block"></i> {{$validationText}}
                        </a>
                    @endif

                    @if($checkStockInThisProduct)
                        <p class="badge bg-success mt-2">Stocked in</p>
                    @endif

                </td>
            </tr>
        @endif
    @endforeach
    </tbody>
@endforeach

@php

    if($setProductValidationTotal == $setProductValidationComplete){
        $setProductValidationDone = true;
    }
@endphp




@section('cusjs')
    @parent
    <script>
        $('fieldset.set_product legend a.setDelete button').addClass('badge bg-danger')
        $('fieldset.set_product legend a.setDelete').addClass('d-inline-block')
    </script>
@endsection
