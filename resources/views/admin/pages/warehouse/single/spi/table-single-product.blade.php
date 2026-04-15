@if(!isset($isAjaxRequest))
<div class="d-none">
    <form></form>
</div>
@php
    global $singleProductValidationComplete;
    global $singleProductValidationTotal;
    global $singleProductValidationDone;
@endphp
@endif
<!----- th => ppi-product.blade.php -->

@foreach ($getSpiProduct as $product)
    @php $checkProductIsSet = $Model('PpiSetProduct')::getSet($product->id); @endphp
    @if(count($checkProductIsSet) > 0)
        <!-- Skip set products -->
    @else
        <tr class="pr_row_{{$product->id}} {{$product->any_warning_cls}}" data-product-id="{{ $product->id }}">
            <!-- Delete & Edit Buttons -->
            <td>
                <a title="Edit" class="edit text-info font-14" href="javascript:void(0)" data-product-id="{{ $product->id }}">
                    <span class="fas fa-edit"></span>
                </a>
                &nbsp;
                <a title="Delete" class="delete text-danger font-14" href="javascript:void(0)" data-product-id="{{ $product->id }}">
                    <span class="fas fa-trash"></span>
                </a>
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

            <!-- Correction Button -->
            <td class="not_print">
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

            <!-- Product Name -->
            <td class="product {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'product')) ? 'text-danger fw-bold' : '' }}">
                <strong>{{ $product->product_name }}</strong>
            </td>

            <!-- Quantity (Editable) -->
            <td class="qty p-1 {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'qty')) ? 'text-danger fw-bold' : '' }}">
                <input type="number" class="form-control form-control-sm qty-input" value="{{ $product->qty }}" min="1" data-old-value="{{ $product->qty }}" data-product-id="{{ $product->id }}">
            </td>

            <!-- Unit -->
            <td class="unit">
                @if($product->product_state == 'Cut-Piece')
                    Bundle
                @else
                    {!! $Model('AttributeValue')::getValueById($product->product_unit_id) !!}
                @endif
            </td>

            <!-- Price (Editable) -->
            <td class="price p-1 ppi_product_price_show {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'price')) ? 'text-danger fw-bold' : '' }}">
                <input type="number" class="form-control form-control-sm unit-price-input" value="{{ $product->unit_price }}" step="0.01" min="0" data-old-value="{{ $product->unit_price }}" data-product-id="{{ $product->id }}">
            </td>

            <!-- Product State -->
            <td class="ppi-info-col">{!! $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state']) !!}</td>

            <!-- Health Status -->
            <td class="ppi-info-col">{!! $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'health_status']) !!}</td>

            <!-- Barcode Format -->
            <td class="not_print ppi-info-col">{!! $product->barcode_format !!}</td>

            <!-- Notes (Editable) -->
            <td class="note p-1">
                <input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="{{ $product->note ?? '' }}" data-old-value="{{ $product->note ?? '' }}" data-product-id="{{ $product->id }}">
            </td>

            <!-- From Warehouse -->
            <td class="ppi-info-col">
                {{ ($product->from_warehouse != $product->warehouse_id) ? 'Lended' : 'Regular' }} <br>
                From {{$Model('Warehouse')::name($product->from_warehouse)}}
            </td>

            <!-- Dispute Note -->
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
                        <span class="class">Correction by {{$Model('User')::getColumn($coorectionData->action_performed_by, 'name')}}</span>
                    </span>
                    @endif
                @endif
            </td>

            <!-- Physical Validation -->
            <td class="text-center not_print">
                @php
                    $checkStockOutThisProduct = false;
                @endphp
                @if(auth()->user()->hasRoutePermission('spi_get_line_item'))
                    <?php if ($productState = $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state'])) {
                        if ($productState == 'Cut-Piece') {
                            $bundleName = $product->bundle_id;
                            $addBundleGetMethod = '?bundle=' . $bundleName;
                        } else {
                            $addBundleGetMethod = null;
                        }
                    }

                    $checkStockOutThisProduct = $Model('PpiSpiStatus')::checkSpiStatus($spi->id, 'spi_product_out_from_stock', ['ppi_spi_product_id' => $product->spi_product_id]);

                    if ($checkStockOutThisProduct) {
                        $validationBgColor = 'green';
                        $validationText = 'Validated';
                        $singleProductValidationComplete += 1;
                    } else {
                        $validationBgColor = 'blue';
                        $validationText = 'Validation';
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

            <!-- Save Button -->
            <td class="not_print text-center">
                <a title="Save" class="save text-success font-14" href="javascript:void(0)" data-product-id="{{ $product->id }}" style="display: none;">
                    <span class="fas fa-save"></span>
                </a>
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
