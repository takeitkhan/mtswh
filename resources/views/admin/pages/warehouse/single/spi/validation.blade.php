@extends('admin.layouts.master')

@section('title')
    Physical Validation
@endsection

@section('onlytitle')
    Physical Validation
    <a href="{{ route('spi_edit', [request()->get('warehouse_code'), $spi_id]) }}"
       class="btn btn-sm btn-outline-primary py-0">Back</a>
@endsection

@section('filter')

@endsection

<?php
/**
 * $product = PPi product
 * call from controller
 * */
?>
@section('content')

    @php
        $warehouse_code = request()->get('warehouse_code');
        $checkThisProductIsDisputeNow = $Model('PpiSpiDispute')::thisPpiProductDisputeOrCoorection('Spi', $product->id);
        $ppiLastStatus = $Model('PpiSpiStatus')::getSpiLastStatus($spi_id, ['ppi_spi_product_id' => $product->id]);
        $ppiLastStatusCode = $ppiLastStatus->code ?? null;
        $ppiLastMainStatus = $Model('PpiSpiStatus')::getSpiLastMainStatus($spi_id);
        $forPrint = [];
    @endphp
    <div class="content-wrapper">
        {{-- <form action="{{ route('ppi_barcode_generator', $warehouse_code) }}" method="GET"> --}}
        {{-- {{ csrf_field() }} --}}
        {{-- <input type="text" name="product_code" /><br/> --}}
        {{-- <input type="submit" name="submit" value="Submit"/> --}}
        {{-- </form> --}}
        <div class="row" id="reload_wrap">
            <div class="col-md-6">
                @if ($checkThisProductIsDisputeNow == 'Dispute'  && $ppiLastMainStatus->code != 'spi_resent_to_wh_manager')
                    <div class="alert alert-warning">
                        This product has been requested for correction
                    </div>
                @else
                    <form id="ppiFormAction" method="post">
                        @csrf
                        <div class="table-wrapper desktop-view mobile-view">
                            @if($product)
                                {{-- @dump($product) --}}
                                @if(auth()->user()->hasRoutePermission('spi_buy_product_form_vendor'))
                                    @if(empty($getLineItem))
                                        <div class="alert alert-warning">
                                            <strong>No product data found.</strong> This might mean:
                                            <ul>
                                                <li>The product hasn't been added to stock in the source warehouse yet</li>
                                                <li>Check if Product Stock records exist for this product</li>
                                                <li>Verify the warehouse assignment for this product</li>
                                            </ul>
                                        </div>
                                    @else
                                        @include('admin.pages.warehouse.single.spi.validation-product-table')
                                    @endif
                                @else
                                    <div class="alert alert-danger">
                                        <strong>Permission Denied:</strong> You don't have permission to view product validation details. 
                                        Please check your role permissions.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-danger">
                                    <strong>Product not found:</strong> Unable to load the product information.
                                </div>
                            @endif
                        </div>
                    </form>
                @endif

            </div>
            <div class="col-md-4">
                <h6>
                    <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0">
                        Information of the selected product
                    </div>
                </h6>
                <!--=====================================
                =========Product Information Table========
                =====================================--->
                <table class="table table-sm table-bordered table-thin">
                    <tbody>
                    @if (!empty($product))
                        <tr>
                            <td style="width: 150px">Product Name</td>
                            <td>
                                @php
                                    $product_name = $Model('Product')::getColumn($product->product_id, 'name');
                                @endphp
                                {{ $product_name }}
                            </td>
                        </tr>
                        <tr>
                            <td>QTY</td>
                            <td>
                                {{ $product->qty }}
                            </td>
                        </tr>

                        <tr>
                            <td>Unit</td>
                            <td>
                                @php
                                    $productUnit = $Query::accessModel('Product')::getColumn($product->product_id, 'unit_id');
                                @endphp
                                {!! $productUnit = $Query::accessModel('AttributeValue')::getValueById($productUnit) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>Product State</td>
                            <td>
                                {{--                                    {!! $product->product_state !!}--}}
                                {!! $productState =  $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'product_state']) !!}
                            </td>
                        </tr>
                        <tr class="ppi_product_price_show">
                            <td>Price</td>
                            <td class="text-dark"> {!! $product->price !!} </td>
                        </tr>
                        <tr>
                            <td>Health Status</td>
                            <td class="text-dark">
                                {{--                                    {!! $product->health_status !!}--}}
                                {!! $healthStatus =  $Model('PpiProduct')::ppiProductInfoByPpiProductId($product->ppi_product_id, ['column' => 'health_status']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>Barcode Format</td>
                            <td class="text-dark">
                                {{ $barcode_format }}
                            </td>
                        </tr>
                        <td>Note</td>
                        <td class="text-dark"> {!! $product->note !!} </td>
                        </tr>
                    @endif
                    </tbody>
                </table>

                <!--===================================
                ============== Button action ==========
                =====================================-->
                @php
                    //dump($product->id);
                    $checkStockOutThisProduct = $Model('PpiSpiStatus')::checkSpiStatus($spi_id, 'spi_product_out_from_stock', ['ppi_spi_product_id' => $product->id]);
                @endphp

                @if($checkStockOutThisProduct)
                    <div class="alert alert-success">This Product is out from stock</div>
                @else

                    @if( auth()->user()->checkUserRoleTypeGlobal() || $ppiLastMainStatus->code == 'spi_resent_to_wh_manager' || $ppiLastMainStatus->code == 'spi_sent_to_wh_manager'  || $ppiLastMainStatus->code == 'spi_dispute_by_wh_manager')

                        @if(auth()->user()->hasRoutePermission('spi_ready_to_physical_validation_action') && $product->from_warehouse == request()->get('warehouse_id'))

                            @if ($checkThisProductIsDisputeNow == 'Dispute')

                            @else
                                <div class="text-start mb-2">
                                    @if ($ppiLastStatusCode == 'spi_agreed_no_dispute')
                                        <div id="btnWrapperBarcodeStockIn" class="d-inline-block">
                                            @if($barcode_format == 'Tag' || $barcode_format ==  'Bundle-Tag')
                                                <a class="btn btn-sm btn-primary py-0" type="button" onclick="PrintDiv()">Start to Print Barcode Tag</a>
                                            @endif
                                            @if($spi->transferable == 'yes')
                                                <span class="alert-warning">Transferable Spi does not allow to single Stock out. Please back to complete the Spi</span>
                                            @else
                                            <a href="#" id="printBarcodeStockIn" class="btn btn-sm btn-success py-0">
                                                Stock Out
                                            </a>
                                            @endif
                                        </div>
                                    @else
                                        @if(auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action'))
                                            <div style="font-size: 11px;">
                                                <input id="agreeallok" type="checkbox" style="height: 12px;"/>
                                                <label for="agreeallok">I agree that there are no dispute.</label>
                                            </div>

                                            <div id="btnWrapper" class="d-inline-block">

                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="alert alert-danger">SPI is waiting for approval of Boss</div>
                    @endif

                    <!-- Buy Section -->
                    @include('admin.pages.warehouse.single.spi.buy_from_vendor')
                @endif




                <!--==============================
                    ======= Dispute Status ==========
                    =============================-->
                    <h6>
                        <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0">
                            Dispute Status
                        </div>
                    </h6>
                    <table class="table table-sm table-bordered table-thin">
                        @php
                            $spiDisputeCorrectionList = $Model('PpiSpiDispute')::ppiDisputeCorrectionList('Spi', $product->id);
                        @endphp
                        <tbody>
                        <tr class="">
                            <td class="alert-danger"><strong>Dispute</strong></td>
                            <td class="alert-success"><strong>Correction</strong></td>
                        </tr>
                        @if(count((array)($spiDisputeCorrectionList)) > 0)
                            @foreach ($spiDisputeCorrectionList as $item)
                                <tr class="align-middle">
                                    <td class="table-danger">
                                        {{ $item->dispute_note }} <br>
                                        Performed By {{ $Model('User')::getColumn($item->dispute_action_by, 'name') }}
                                        at
                                        {{ $item->dispute_date }}
                                    </td>
                                    <td class="table-success">
                                        @if ($item->correction_dispute_id)
                                            <i class="fa fa-check-circle m-0 h3 w-auto text-success"
                                               style="font-size: 15px;"></i>
                                            Performed
                                            By {{ $Model('User')::getColumn($item->correction_action_by, 'name') }}
                                            at {{ $item->correction_date }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="2">There are no dispute issue</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>

                    <!--==============================
                    ======= Print Barcode Section =====
                    =============================-->
                    @if($barcode_format == 'Tag' || $barcode_format == 'Bundle-Tag')
                        <div id="printDiv" style="display:none;">
                            <style>
                                body.print-mode { 
                                    margin: 0; 
                                    padding: 10px;
                                }
                                @media print {
                                    body {
                                        margin: 0;
                                        padding: 0;
                                    }
                                }
                            </style>
                            @foreach ($forPrint as $barcode)
                                {!! $barcode !!}
                            @endforeach
                        </div>
                    @endif

                    <?php /*
                    ppi_spi_status_id<br/> kon action perform korsi tar id
                    ppi_spi_id<br/> eta ppi hole ppi id / spi hole spi id
                    status_for <br/> eta ppi hole ppi status / spi hole spi status
                    ppi_spi_product_id <br/> ppi_products table er id ekhane ashbe
                    issue_column <br/> kon column problem chilo dispute er somoy
                    note <br/> any note
                    action_format<br/> dispute / correction kon action perform holo
                    */
                    ?>


                        <!-- Purchase Summary -->
                @php
                    $PurchaseData = $Model('PurchaseVendor')::where('spi_id', $spi->id)->where('spi_product_id', $product->id)->get();
                @endphp
                @if(count($PurchaseData) > 0)
                <h6>
                    <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0">
                        Purchase Summary
                    </div>
                </h6>
                <table class="table table-sm table-bordered table-thin">
                    <tr class="">
                        <td class="alert-danger"><strong>Vendor</strong></td>
                        <td class="alert-success"><strong>Qty</strong></td>
                        <td class="alert-success"><strong>Price</strong></td>
                        <td class="alert-success"><strong>Date</strong></td>
                    </tr>

                    <tr>
                        @foreach($PurchaseData as $data)
                            <td>{{$data->vendor_name}}</td>
                            <td>{{$data->qty}}</td>
                            <td>{{$data->price}}</td>
                            <td>{{$data->created_at->format('d/m/Y')}}</td>
                        @endforeach
                    </tr>
                </table>
                    @endif


            </div>
            <div class="col-md-2">
                <h6>
                    <div class="title-with-border mb-0 alert-secondary px-2 text-dark border-0">
                        SPI ID : {{ $spi_id }}
                    </div>
                </h6>
                @include('admin.pages.warehouse.single.spi.spi-status')
            </div>
        @endsection






        <!--==============================
        =====================================
        Existing Product Verification Modal
        =================================-->

        <div id="reload_modal">
            <?php echo $Component::bootstrapModal('existingProduct', ['modalHeader' => 'Scan Barcode', 'position' => 'right', 'backdrop' => true, 'saveBtn' => false, 'use' => 'class']); ?>
        </div>


        @section('cusjs')

            @php
                /**
                 * ppi Elements Setup
                 * Show / Hide or Any Permission use for Button , row a
                 */
                echo $PpiSpiPermission::elements();

                /** DisputeBtn Modal */

                $disputeRouteAction = route('spi_dispute_by_wh_manager_action', [request()->get('warehouse_code'), $spi_id, 'spi_dispute_by_wh_manager']);
                $iAgreeThereAreNoDisputeRoute = route('spi_agreed_no_dispute_action', [request()->get('warehouse_code'), $spi_id, 'spi_agreed_no_dispute']) . '?with-note=with ' . $product_name.'&&with-spi_product_id='.$product->id;
                $iAgreeThereAreNoExistingRoute = route('ppi_agreed_no_existing_action', [request()->get('warehouse_code'), $spi_id, 'ppi_agreed_no_existing']) . '?with-note=with ' . $product_name.'&&with-spi_product_id='.$product->id;
            @endphp

            {{-- Dispute Modal --}}
            {!! $Component::bootstrapModal('disputeBtn', ['btnWrapperId' => 'btnWrapper', 'saveBtn' => false, 'backdrop' => true, 'formAction' => $disputeRouteAction]) !!}

            {{-- if Agree there are no Dispute Form Modal --}}
            {!! $Component::jsModal('noDisputeModal', ['btnWrapperId' => 'btnWrapper', 'formAction' => $iAgreeThereAreNoDisputeRoute, 'modalHeader' => 'Are you confirm', 'modalSubHeader' => 'There are no dispute products']) !!}

            {{--    --}}{{-- if Agree there are no Existing Form Modal --}}
            {{--    {!! $Component::jsModal('noExistingModal', ['btnWrapperId' => 'btnWrapperEx', 'formAction' => $iAgreeThereAreNoExistingRoute, 'modalHeader' => 'Are you confirm', 'modalSubHeader' => 'There are no existing products']) !!}--}}


            <!-- ==========================
        ======Dispute Form ===========
        =========================== -->

                <script type="text/template" id="disputeForm">
                    <input type="hidden" name="dispute_ele[{{ $product->id }}][spi_product_id]"
                           value="{{ $product->id }}">
                    <input type="hidden" name="dispute_ele[{{ $product->id }}][action_format]" value="Dispute">
                    <div class="form-check text-dark text-center">
                        <input type="checkbox" id="{{ $product->id }}p_product" class="w-auto checkItem ms-3 me-0"
                               name="dispute_ele[{{ $product->id }}][issue_column][product]" id="" value="product">
                        <label class="w-auto" for="{{ $product->id }}p_product">Product</label>
                        <input type="checkbox" id="{{ $product->id }}p_qty" class="w-auto checkItem ms-3 me-0"
                               name="dispute_ele[{{ $product->id }}][issue_column][qty]" id="" value="qty">
                        <label class="w-auto" for="{{ $product->id }}p_qty">Quantity</label>
                    <!--
                        <input type="checkbox" id="{{ $product->id }}p_price" class="w-auto checkItem ms-3 me-0" name="dispute_ele[{{ $product->id }}][issue_column][price]" value="price">
                        <label class="w-auto" for="{{ $product->id }}p_price">Price</label>
                        -->
                    </div>
                    <label class="w-auto" for="">Details of issue</label>
                    <textarea required class="form-control" name="dispute_ele[{{ $product->id }}][note]"
                              placeholder="Details of issue"></textarea>
                    <div class="d-grid gap-2">
                        <button type="submit" onclick="return confirm('Are you sure want to dispute this item?');" class="btn btn-outline-primary btn-sm mt-2">Submit</button>
                    </div>
                </script>


                <script>
                    // Initialize on DOM ready
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', initializeValidation);
                    } else {
                        initializeValidation();
                    }

                    function initializeValidation() {
                        // Button definitions
                        let dispute = '<a id="disputeBtn" class="btn btn-sm btn-outline-danger py-0">Dispute</a>';
                        let iAgreeThereAreNoDispute = '<button type="button" id="noDisputeModal" class="btn btn-sm btn-primary py-0">Save</button>';
                        let iAgreeThereAreNoExisting = '<button type="button" id="noExistingModal" class="btn btn-sm btn-primary py-0">Save</button>';
                        let printBarcodeStockIn = '<a href="#" id="printBarcodeStockIn" class="btn btn-sm btn-outline-primary py-0">Print Barcode and Stock In</a>';

                        // Dispute button loader function
                        function disputeBtnLoaded() {
                            let btnWrapper = document.getElementById('btnWrapper');
                            if (btnWrapper) btnWrapper.innerHTML = dispute;
                        }

                        // Initialize dispute button
                        disputeBtnLoaded();

                        // Set dispute form content
                        let disputeBtnModalBody = document.getElementById('disputeBtnModalBody');
                        let disputeFormTemplate = document.getElementById('disputeForm');
                        if (disputeBtnModalBody && disputeFormTemplate) {
                            disputeBtnModalBody.innerHTML = disputeFormTemplate.innerHTML;
                        }

                        // Check all checkbox handler
                        let checkAllCheckBox = document.getElementById('checkAllCheckBox');
                        if (checkAllCheckBox) {
                            checkAllCheckBox.addEventListener('click', function() {
                                let isChecked = this.checked;
                                document.querySelectorAll('input#barcode_product_line_item').forEach(function(el) {
                                    el.checked = isChecked;
                                });
                            });
                        }

                        // Agree to no dispute checkbox handler
                        let agreeAllOkCheckbox = document.getElementById('agreeallok');
                        if (agreeAllOkCheckbox) {
                            agreeAllOkCheckbox.addEventListener('click', function() {
                                let btnWrapper = document.getElementById('btnWrapper');
                                if (btnWrapper) {
                                    if (this.checked) {
                                        btnWrapper.innerHTML = iAgreeThereAreNoDispute;
                                    } else {
                                        disputeBtnLoaded();
                                    }
                                }
                            });
                        }

                        // Agree to no existing checkbox handler
                        let agreeNoExistingCheckboxes = document.querySelectorAll('input#agreeNoExisting');
                        agreeNoExistingCheckboxes.forEach(function(checkbox) {
                            checkbox.addEventListener('click', function() {
                                let btnWrapperEx = document.getElementById('btnWrapperEx');
                                if (btnWrapperEx) {
                                    if (this.checked) {
                                        btnWrapperEx.innerHTML = iAgreeThereAreNoExisting;
                                    } else {
                                        btnWrapperEx.innerHTML = '';
                                    }
                                }
                            });
                        });

                        // Print barcode and stock in handler
                        let btnWrapperBarcodeStockIn = document.getElementById('btnWrapperBarcodeStockIn');
                        if (btnWrapperBarcodeStockIn) {
                            btnWrapperBarcodeStockIn.addEventListener('click', function(e) {
                                if (e.target && e.target.id === 'printBarcodeStockIn') {
                                    e.preventDefault();
                                    let barcodeRoute = "{{ route('spi_product_stock_out', [request()->get('warehouse_code')]) }}";
                                    let stockInForm = document.getElementById('ppiFormAction');
                                    if (stockInForm) {
                                        stockInForm.action = barcodeRoute;
                                        let hasChecked = document.querySelector('input#barcode_product_line_item:checked');
                                        if (typeof confirmAlert === 'function') {
                                            confirmAlert('Are you ready to stock out the product', '', '#ppiFormAction');
                                        }
                                    }
                                }
                            });
                        }

                        // Print Div Function
                        window.PrintDiv = function() {
                            let printDiv = document.getElementById('printDiv');
                            if (printDiv) {
                                let printContents = printDiv.innerHTML;
                                let originalContents = document.body.innerHTML;
                                document.body.innerHTML = printContents;
                                window.print();
                                document.body.innerHTML = originalContents;
                                location.reload();
                            }
                        }
                    }
                </script>

                <!--==============================
                ======= Existing Product Verification ========
                =============================-->

                <script>
                    /**
                     * Existing Product Verification
                     */
                    let barcodeInputField = '#existingProductBarcode';
                    let barcodeInputHiddenBarcodeField = '#existingPpiProductHiddenBarcode';
                    let barcodeInputHiddenOrginalBarcodeField = '#existingPpiProductHiddenOrginalBarcode';
                    let productIdInputField = '#existingProductId';
                    let productQtyInputField = '#existingProductQty';
                    let spiProductIdInputField = '#existingPpiProductId';
                    let productUniquekeyInputField = '#existingProductUniqueKey';

                    // Event delegation for existing product verify button
                    document.addEventListener('click', function(e) {
                        if (e.target && e.target.classList && e.target.classList.contains('existingProduct')) {
                            e.preventDefault();
                            let button = e.target;
                            let getThisBarcode = button.getAttribute('data-barcode');
                            let getThisOrginalBarcode = button.getAttribute('data-orginal_barcode');
                            let getThisProductId = button.getAttribute('data-product_id');
                            let getThisSpiProductId = button.getAttribute('data-spi_product_id');
                            let getThisProductUniqueKey = button.getAttribute('data-product_unique_key');
                            let getThisProductQty = button.getAttribute('data-product_qty');

                            let ExistingModalBody = document.getElementById('existingProductModalBody');
                            if (ExistingModalBody) {
                                let ExistingModalHtml = `
                                    <div class="existingProductModalWrap">
                                        <div class="form-group">
                                            <label>Click On Input box before scan Barcode</label>
                                            <input type="text" class="form-control form-control-sm" value="" id="existingProductBarcode">
                                        </div>
                                        <div class="orginalBarcodeShow"></div>
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisBarcode}" id="existingPpiProductHiddenBarcode">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisOrginalBarcode}" id="existingPpiProductHiddenOrginalBarcode">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisSpiProductId}" id="existingPpiProductId">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisProductQty}" id="existingProductQty">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisProductId}" id="existingProductId">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisProductUniqueKey}" id="existingProductUniqueKey">
                                        <div class="saveBtnForExistingProductStockIn"></div>
                                    </div>
                                `;
                                ExistingModalBody.innerHTML = ExistingModalHtml;
                                let barcodeInput = document.getElementById('existingProductBarcode');
                                if (barcodeInput) {
                                    setTimeout(() => barcodeInput.focus(), 100);
                                }
                            }
                        }
                    });

                    // Existing Product Stock Out Function
                    function existingStock(hiddenBarcode) {
                        let barcodeInputElement = document.getElementById('existingProductBarcode');
                        let barcodeHiddenElement = document.getElementById('existingPpiProductHiddenBarcode');
                        let barcodeHiddenOrginalElement = document.getElementById('existingPpiProductHiddenOrginalBarcode');
                        let productIdElement = document.getElementById('existingProductId');
                        let spiProductIdElement = document.getElementById('existingPpiProductId');
                        let productQtyElement = document.getElementById('existingProductQty');
                        let productUniqueKeyElement = document.getElementById('existingProductUniqueKey');

                        if (!barcodeInputElement) return;

                        let barcode = barcodeInputElement.value || '';
                        let barcodeHidden = barcodeHiddenElement ? barcodeHiddenElement.value : '';
                        let barcodeHiddenOrginal = barcodeHiddenOrginalElement ? barcodeHiddenOrginalElement.value : '';
                        let productId = productIdElement ? productIdElement.value : '';
                        let spiProductId = spiProductIdElement ? spiProductIdElement.value : '';
                        let productQty = productQtyElement ? productQtyElement.value : '';
                        let productUniqueKey = productUniqueKeyElement ? productUniqueKeyElement.value : '';

                        // Close modal if exists
                        let modal = document.getElementById('existingProductOpenModal');
                        if (modal && typeof bootstrap !== 'undefined') {
                            try {
                                bootstrap.Modal.getInstance(modal)?.hide();
                            } catch(e) {}
                        }

                        if (hiddenBarcode && hiddenBarcode === barcodeHidden) {
                            // Use fetch API instead of $.ajax
                            fetch('{{ route("ppi_existing_product_check_during_stock", request()->get("warehouse_code")) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    'spi_id': '{{ $spi_id }}',
                                    'barcode': hiddenBarcode,
                                    'product_qty': productQty,
                                    'replace_with_barcode': barcodeHiddenOrginal,
                                    'product_id': productId,
                                    'spi_product_id': spiProductId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
                                if (data.status == 1) {
                                    if (typeof toastr !== 'undefined') {
                                        toastr.success(data.message);
                                    } else {
                                        alert(data.message);
                                    }
                                    // Reload wrapper
                                    let reloadWrap = document.getElementById('reload_wrap');
                                    if (reloadWrap) {
                                        location.reload();
                                    }
                                } else if (data.status == 0) {
                                    alert(`<h5>${hiddenBarcode}</h5> This product was not found in the Database as Stock Out`);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('An error occurred while processing your request');
                            });
                        } else {
                            alert(`<h5>${barcodeHidden}</h5> You have selected wrong item`);
                        }
                    }

                    // Keypress and paste handler for barcode input
                    document.addEventListener('keypress', function(e) {
                        if (e.target && e.target.id === 'existingProductBarcode') {
                            if (e.which === 13) { // Enter key
                                e.preventDefault();
                                let hiddenBarcode = document.getElementById('existingPpiProductHiddenBarcode');
                                if (hiddenBarcode) {
                                    existingStock(hiddenBarcode.value);
                                }
                            }
                        }
                    });

                    document.addEventListener('paste', function(e) {
                        if (e.target && e.target.id === 'existingProductBarcode') {
                            e.preventDefault();
                            setTimeout(() => {
                                let barcodeValue = e.target.value;
                                let saveBtnDiv = document.querySelector('#reload_modal .saveBtnForExistingProductStockIn');
                                if (saveBtnDiv) {
                                    let submitBtn = `<button type="button" class="btn btn-primary btn-sm mt-2" onclick="existingStock('${barcodeValue}')">Submit</button>`;
                                    saveBtnDiv.innerHTML = submitBtn;
                                }
                            }, 100);
                        }
                    });

                    // Submit button handler for existing product
                    document.addEventListener('click', function(e) {
                        if (e.target && 
                            e.target.parentElement?.classList?.contains('saveBtnForExistingProductStockIn') ||
                            e.target.classList?.contains('existingProductSubmit')) {
                            e.preventDefault();
                            let hiddenBarcode = document.getElementById('existingPpiProductHiddenBarcode');
                            if (hiddenBarcode) {
                                existingStock(hiddenBarcode.value);
                            }
                        }
                    });

                    //Action if click on submit button
                    $(document).on('click', '#existingProductOpenModal .saveBtnForExistingProductStockIn button', function (e) {
                        e.preventDefault()
                        let thisDataInputVal = $(this).data('input_val')
                        existingStock(thisDataInputVal);
                    })
                </script>


    @endsection
