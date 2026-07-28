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
            // The current row's own waiting quantity is excluded so it can be edited up to the remaining PPI allocation.
            $stockNumeric = 0;
            $debugInfo = [
                'ppi_id' => $product->ppi_id ?? 'NULL',
                'product_id' => $product->product_id ?? 'NULL',
                'ppi_product_id' => $product->ppi_product_id ?? 'NULL',
                'qty_requested' => $product->qty ?? 0
            ];

            $ppiProduct = DB::table('ppi_products')
                ->where('ppi_id', $product->ppi_id)
                ->where('product_id', $product->product_id)
                ->whereNull('deleted_at')
                ->first();

            if ($ppiProduct) {
                $waitingQty = (float)DB::table('temporary_stocks as temporary_stock')
                    ->join('spi_products as spi_product', 'spi_product.id', '=', 'temporary_stock.spi_product_id')
                    ->where('spi_product.ppi_id', $ppiProduct->ppi_id)
                    ->where('spi_product.product_id', $ppiProduct->product_id)
                    ->where('temporary_stock.action_format', 'Spi')
                    ->where('temporary_stock.spi_product_id', '!=', $product->id)
                    ->sum('temporary_stock.waiting_stock_out');

                $stockedOutQty = (float)DB::table('product_stocks as product_stock')
                    ->join('spi_products as spi_product', 'spi_product.id', '=', 'product_stock.ppi_spi_product_id')
                    ->where('spi_product.ppi_id', $ppiProduct->ppi_id)
                    ->where('spi_product.product_id', $ppiProduct->product_id)
                    ->where('product_stock.action_format', 'Spi')
                    ->where('product_stock.stock_action', 'Out')
                    ->sum('product_stock.qty');

                $stockNumeric = max(0, (float)$ppiProduct->qty - $waitingQty - $stockedOutQty);
                $debugInfo['found'] = true;
                $debugInfo['stock'] = $stockNumeric;
                $debugInfo['waiting_other_rows'] = $waitingQty;
                $debugInfo['stocked_out'] = $stockedOutQty;
            } else {
                $debugInfo['found'] = false;
                $debugInfo['note'] = 'No PPI product found with ppi_id + product_id';
            }
            
        @endphp

        <tr class="pr_row_{{$product->id}} {{$product->any_warning_cls}}" data-product-id="{{ $product->id }}" data-available-qty="{{ $stockNumeric }}" data-ppi-id="{{ $product->ppi_id }}" data-product-id-fk="{{ $product->product_id }}" data-debug="{{ json_encode($debugInfo) }}">
            <!-- Delete & Edit Buttons -->
            <td>
                @php
                    // Get current SPI status
                    $lastSpiStatus = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi->id)
                        ->where('status_for', 'Spi')
                        ->where('status_format', 'Main')
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    // Get current user roles
                    $userRoles = DB::table('role_users')
                        ->join('roles', 'roles.id', '=', 'role_users.role_id')
                        ->where('role_users.user_id', auth()->user()->id)
                        ->pluck('roles.code')
                        ->toArray();
                    
                    $isBoss = in_array('boss', $userRoles);
                    $isWhManager = in_array('warehouse_manager', $userRoles) || in_array('wh_manager', $userRoles);
                    
                    // Check if SPI is locked for current user
                    $bossLockedStatuses = ['spi_sent_to_wh_manager', 'spi_resent_to_wh_manager', 'spi_ready_to_physical_validation', 'spi_all_steps_complete'];
                    $isProductLocked = false;
                    
                    if ($lastSpiStatus && in_array($lastSpiStatus->code, $bossLockedStatuses)) {
                        if ($isBoss) {
                            // Boss is locked unless there's a dispute
                            if (!in_array($lastSpiStatus->code, ['spi_dispute_by_wh_manager', 'spi_correction_done_by_boss'])) {
                                $isProductLocked = true;
                            }
                        }
                        // WH Manager can edit when sent to them
                    }
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
            @php
                // Get the actual quantity from temporary_stocks (manager's confirmed quantity)
                $tempStock = DB::table('temporary_stocks')
                    ->where('spi_product_id', $product->id)
                    ->where('action_format', 'Spi')
                    ->first();
                
                $displayQty = $product->qty; // Default to spi_products qty
                
                // If temporary_stocks exists, use the manager's confirmed quantity
                if ($tempStock) {
                    $displayQty = $tempStock->waiting_stock_out;
                }
            @endphp
            <td class="qty p-1 {{!empty($Model('PpiSpiDispute')::checkProductForDispute('Spi', $spi->id, $product->id, 'qty')) ? 'text-danger fw-bold' : '' }}">
                @if($isProductLocked)
                    <span class="badge bg-secondary">{{ $displayQty }} (Readonly)</span>
                @else
                    <input type="number" class="form-control form-control-sm qty-input" value="{{ $displayQty }}" min="1" data-old-value="{{ $displayQty }}" data-product-id="{{ $product->id }}" data-max-available="{{ $stockNumeric }}">
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
                @if($isProductLocked)
                    <span class="badge bg-secondary">{{ number_format($product->unit_price, 2) }} (Readonly)</span>
                @else
                    <input type="number" class="form-control form-control-sm unit-price-input" value="{{ $product->unit_price }}" step="0.01" min="0" data-old-value="{{ $product->unit_price }}" data-product-id="{{ $product->id }}">
                @endif
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

            <!-- Project -->
            <td style="background-color: #f0fff8; font-weight: bold; font-size: 12px; text-align: center;">
                @php
                    $projectName = 'N/A';
                    if($product->ppi_id) {
                        $ppiSpi = DB::table('ppi_spis')
                            ->where('id', $product->ppi_id)
                            ->first();
                        if($ppiSpi) {
                            $projectName = $ppiSpi->project ?? 'N/A';
                        }
                    }
                @endphp
                <span class="badge bg-info">{{ $projectName }}</span>
            </td>

            @php
                // Detect if current user is Boss (for Stock In Hand display)
                $userRoles = DB::table('role_users')->join('roles', 'roles.id', '=', 'role_users.role_id')
                    ->where('role_users.user_id', auth()->user()->id)
                    ->pluck('roles.name', 'roles.code')->toArray();
                $isBossUser = isset($userRoles['boss']) || in_array('Boss', $userRoles);
                
                // Check if user has Managers As SM role (code: managers_as_sm)
                $isManagersSM = isset($userRoles['managers_as_sm']) || in_array('Managers As SM', $userRoles);
                
                // Hide Stock in Hand if user is Managers As SM
                $showStockInHand = $isBossUser && !$isManagersSM;
                
                // Total QTY in this specific PPI for this product
                // Sum all ppi_products entries for this product_id in this PPI
                $totalQtyInThisPpi = DB::table('ppi_products')
                    ->where('ppi_id', $product->ppi_id)
                    ->where('product_id', $product->product_id)
                    ->sum('qty') ?? 0;

                // Total QTY in this specific SPI for this product (from this specific PPI only)
                $totalQtyInThisSpi = DB::table('spi_products')
                    ->where('spi_id', $spi->id)
                    ->where('product_id', $product->product_id)
                    ->where('ppi_id', $product->ppi_id)
                    ->sum('qty') ?? 0;

                // Stock in Hand = Total in PPI - Total in SPI
                $stockInHandCalculated = $totalQtyInThisPpi - $totalQtyInThisSpi;
            @endphp

            @if($showStockInHand)
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
                @if($isProductLocked)
                    <span class="badge bg-secondary">{{ $product->note ?? 'N/A' }} (Readonly)</span>
                @else
                    <input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="{{ $product->note ?? '' }}" data-old-value="{{ $product->note ?? '' }}" data-product-id="{{ $product->id }}">
                @endif
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
