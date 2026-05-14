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
        @php
            // Pre-calculate stock for this row - moved before tr tag
            $stockInHand = 'N/A';
            $stockNumeric = 0;
            $debugInfo = [
                'ppi_id' => $product->ppi_id ?? 'NULL',
                'product_id' => $product->product_id ?? 'NULL',
                'ppi_product_id' => $product->ppi_product_id ?? 'NULL',
                'qty_requested' => $product->qty ?? 0
            ];
            
            // Try ppi_product_id first (direct ID from ppi_products table)
            if($product->ppi_product_id) {
                $ppiProduct = DB::table('ppi_products')
                    ->where('id', $product->ppi_product_id)
                    ->first();
                
                if($ppiProduct) {
                    $stockInHand = $ppiProduct->qty ?? 'N/A';
                    $stockNumeric = (int)($ppiProduct->qty ?? 0);
                    $debugInfo['found'] = true;
                    $debugInfo['stock'] = $stockNumeric;
                    $debugInfo['lookup_method'] = 'ppi_product_id';
                } else {
                    $debugInfo['found'] = false;
                    $debugInfo['note'] = 'ppi_product_id lookup failed';
                }
            } 
            // Fallback to ppi_id + product_id
            else if($product->ppi_id) {
                $ppiProduct = DB::table('ppi_products')
                    ->where('ppi_id', $product->ppi_id)
                    ->where('product_id', $product->product_id)
                    ->first();
                
                if($ppiProduct) {
                    $stockInHand = $ppiProduct->qty ?? 'N/A';
                    $stockNumeric = (int)($ppiProduct->qty ?? 0);
                    $debugInfo['found'] = true;
                    $debugInfo['stock'] = $stockNumeric;
                    $debugInfo['lookup_method'] = 'ppi_id + product_id';
                } else {
                    $debugInfo['found'] = false;
                    $debugInfo['note'] = 'No PPI product found with ppi_id + product_id';
                }
            } else {
                $debugInfo['note'] = 'No ppi_product_id or ppi_id';
            }
            
            $qtyRequested = (int)$product->qty;
            // Check if qty exceeds stock (even if stock is 0, still show as exceeded)
            $isQtyExceeded = ($qtyRequested > $stockNumeric);
            $rowClass = $isQtyExceeded ? 'table-danger' : '';
        @endphp

        <tr class="pr_row_{{$product->id}} {{$product->any_warning_cls}} {{ $rowClass }}" data-product-id="{{ $product->id }}" data-stock-available="{{ $stockNumeric }}" data-debug="{{ json_encode($debugInfo) }}">
            <!-- Delete & Edit Buttons -->
            <td>
                @php
                    $isProductLocked = \App\Helpers\Warehouse\PpiSpiHelper::isLockedForCurrentUser($spi->id, 'Spi');
                @endphp
                @if(!$isProductLocked)
                    <a title="Edit" class="edit text-info font-14" href="javascript:void(0)" data-product-id="{{ $product->id }}">
                        <span class="fas fa-edit"></span>
                    </a>
                    &nbsp;
                    <a title="Delete" class="delete text-danger font-14" href="javascript:void(0)" data-product-id="{{ $product->id }}">
                        <span class="fas fa-trash"></span>
                    </a>
                @endif
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

            <!-- Product Name with Details -->
            <td class="product {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'product')) ? 'text-danger fw-bold' : '' }}">
                <strong>{{ $product->product_name }}</strong>
                <div style="font-size: 0.85rem; color: #666; margin-top: 4px; line-height: 1.4;">
                    <div><small><strong>State:</strong> {!! $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state']) !!}</small></div>
                    <div><small><strong>Health:</strong> {!! $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'health_status']) !!}</small></div>
                    <div><small><strong>Barcode:</strong> {!! $product->barcode_format !!}</small></div>
                </div>
            </td>

            <!-- Quantity (Editable) -->
            <td class="qty p-1 {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'qty')) ? 'text-danger fw-bold' : '' }}">
                <input type="number" class="form-control form-control-sm qty-input" value="{{ $product->qty }}" min="1" data-old-value="{{ $product->qty }}" data-product-id="{{ $product->id }}" data-max-available="{{ $stockNumeric }}" {{ $isProductLocked ? 'disabled' : '' }}>
                @php
                    // Get user roles to check if boss
                    $userRolesForButtonCheck = DB::table('role_users')->join('roles', 'roles.id', '=', 'role_users.role_id')
                        ->where('role_users.user_id', auth()->user()->id)
                        ->pluck('roles.name', 'roles.code')->toArray();
                    $isBossForButtons = isset($userRolesForButtonCheck['boss']) || in_array('Boss', $userRolesForButtonCheck);
                @endphp
                @if($isQtyExceeded && $isBossForButtons)
                    <small class="text-danger fw-bold d-block mt-1">⚠ Shortfall: {{ $qtyRequested - $stockNumeric }} units</small>
                    <div class="mt-1 d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-warning adjust-to-available" data-product-id="{{ $product->id }}" data-max-available="{{ $stockNumeric }}">
                            <i class="fas fa-sync"></i> Adjust
                        </button>
                        <button type="button" class="btn btn-sm btn-info add-from-another-ppi" data-product-id="{{ $product->product_id }}" data-ppi-id="{{ $product->ppi_id }}" data-product-name="{{ $product->product_name }}" data-shortfall="{{ $qtyRequested - $stockNumeric }}" data-bs-toggle="modal" data-bs-target="#alternativePpiModal">
                            <i class="fas fa-plus-circle"></i> Add from other PPI
                        </button>
                    </div>
                @endif
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
                <input type="number" class="form-control form-control-sm unit-price-input" value="{{ $product->unit_price }}" step="0.01" min="0" data-old-value="{{ $product->unit_price }}" data-product-id="{{ $product->id }}" {{ $isProductLocked ? 'disabled' : '' }}>
            </td>

            <!-- PPI ID -->
            <td style="background-color: #f0f8ff; font-weight: bold; font-size: 12px; text-align: center;">
                {{ $product->ppi_id ?? 'N/A' }}
            </td>

            <!-- Site Code -->
            <td style="background-color: #fff8f0; font-weight: bold; font-size: 12px; text-align: center;">
                @php
                    $siteCode = 'N/A';
                    if($spi && $spi->source) {
                        // Get the latest site code from SPI sources
                        $latestSource = $spi->source()
                            ->orderBy('id', 'desc')
                            ->first();
                        if($latestSource) {
                            $siteCode = $latestSource->who_source ?? 'N/A';
                        }
                    }
                @endphp
                {{ $siteCode }}
            </td>

            @php
                // Detect if current user is Boss (for Stock In Hand display)
                $userRoles = DB::table('role_users')->join('roles', 'roles.id', '=', 'role_users.role_id')
                    ->where('role_users.user_id', auth()->user()->id)
                    ->pluck('roles.name', 'roles.code')->toArray();
                $isBossUser = isset($userRoles['boss']) || in_array('Boss', $userRoles);
                
                // Total QTY in this specific PPI for this product
                $totalQtyInThisPpi = 0;
                
                // Get the actual PPI ID from ppi_products table using ppi_product_id
                if($product->ppi_product_id) {
                    $actualPpiProduct = DB::table('ppi_products')
                        ->where('id', $product->ppi_product_id)
                        ->first();
                    
                    if($actualPpiProduct) {
                        $totalQtyInThisPpi = $actualPpiProduct->qty ?? 0;
                    }
                }

                // Total QTY in this specific SPI for this product
                $totalQtyInThisSpi = DB::table('spi_products')
                    ->where('spi_id', $spi->id)
                    ->where('product_id', $product->product_id)
                    ->sum('qty') ?? 0;

                // Stock in Hand = Total in PPI - Total in SPI
                $stockInHandCalculated = $totalQtyInThisPpi - $totalQtyInThisSpi;
            @endphp

            @if($isBossUser)
                <!-- Total QTY in this PPI -->
                <td style="background-color: #ffe8e8; font-weight: bold; text-align: center;">
                    {{ $totalQtyInThisPpi }}
                </td>

                <!-- Total QTY in this SPI -->
                <td style="background-color: #e8f0ff; font-weight: bold; text-align: center;">
                    {{ $totalQtyInThisSpi }}
                </td>

                <!-- Stock in Hand (PPI Total - SPI Total) -->
                <td class="stock-in-hand-col" style="background-color: #e8f4f8; font-weight: bold; text-align: center;">
                    {{ $stockInHandCalculated }}
                </td>
            @endif

            <!-- Notes (Editable) -->
            <td class="note p-1">
                <input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="{{ $product->note ?? '' }}" data-old-value="{{ $product->note ?? '' }}" data-product-id="{{ $product->id }}" {{ $isProductLocked ? 'disabled' : '' }}>
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
                @if(!$isProductLocked && auth()->user()->hasRoutePermission('spi_get_line_item'))
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
