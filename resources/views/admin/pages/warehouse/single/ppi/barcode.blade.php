@extends('admin.layouts.master')

@section('title')
    Physical Validation
@endsection

@section('onlytitle')
    Physical Validation
    <a href="{{ route('ppi_edit', [request()->get('warehouse_code'), $ppi_id]) }}"
       class="py-0 btn-outline-primary btn btn-sm">Back</a>
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
        $checkThisProductIsDisputeNow = $Model('PpiSpiDispute')::thisPpiProductDisputeOrCoorection('Ppi', $product->id);
        $ppiLastStatus = $Model('PpiSpiStatus')::getPpiLastStatus($ppi_id, ['ppi_spi_product_id' => $product->id]);
        $ppiLastStatusCode = $ppiLastStatus->code ?? null;
         $ppiLastMainStatus = $Model('PpiSpiStatus')::getPpiLastMainStatus($ppi_id);
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
{{--                @if ($checkThisProductIsDisputeNow == 'Dispute' || ($checkThisProductIsDisputeNow == 'Correction'  && $ppiLastMainStatus->code != 'ppi_resent_to_wh_manager'))--}}
                @if ($checkThisProductIsDisputeNow == 'Dispute'  && $ppiLastMainStatus->code != 'ppi_resent_to_wh_manager')
                    <div class="alert alert-warning">
                        This product has been requested for correction
                    </div>
                @else
                    <form id="ppiFormAction" method="post">
                        @csrf
                        <div class="table-wrapper desktop-view mobile-view">
                            @if ($product)
                                {{-- @dump($product) --}}
                                <table>
                                    <thead>
                                    <tr>
                                        <th>
                                        {{-- <input type="checkbox" id="checkAllCheckBox" class="mb-0 h-auto" value=""> --}}
                                        </td>
                                        <th class="text-center">Product Name
                                        </td>
                                        @if($bundle_product)
                                            <th class="text-center">Name Of Bundle</th>
                                            <th class="text-center">Qty Of Bundle</th>
                                        @else
                                            <th class="text-center">Qty</th>
                                        @endif
                                        <th class="text-center" width="120px">Barcode Digit
                                        </td>
                                        <th class="text-center" width="200px">Action
                                        </td>
                                    </tr>
                                    </thead>
                                    <input type="hidden" name="ppi_id" value="{{ $product->ppi_id }}"/>
                                    <input type="hidden" name="ppi_product_id" value="{{ $product->id }}"/>
                                    <input type="hidden" name="product_id" value="{{ $product->product_id }}"/>
                                    <input type="hidden" name="product_unique_key" value="{{ $unique_key }}"/>
                                    <input type="hidden" name="warehouse_id" value="{{ $product->warehouse_id }}"/>
                                    <tbody>
                                    @for ($i = 0; $i < $total_row; $i++)
                                        @php
                                            if($bundle_product){
                                                $debp =$bundle_product[$i]['bundle_name'];
                                                //dd($debp);
                                                $barCodeDigit = $unique_key . $product->id.$debp.$i;
                                                $orginalBarCodeDigit = $unique_key . $product->id.$debp.$i;
                                                //dd($barcode_prefix);
                                            }else {
                                                $barCodeDigit = $unique_key . $product->id . $i;
                                                $orginalBarCodeDigit = $barcode_prefix . $product->id . $i;
                                            }
                                            //dump($barcode_prefix);
                                            $thisProductId = $Model('Product')::getColumn($product->product_id, 'id');
                                            $checkExistingWithDB = $Model('ProductStock')
                                                                ::where('product_id', $thisProductId)
                                                                //->where('barcode', $barCodeDigit)
                                                                ->where('note', 'replace_with_'.$orginalBarCodeDigit)
                                                                ->where('stock_type', 'Existing')
                                                                ->where('ppi_spi_id', $ppi_id)
                                                                ->where('action_format', 'Ppi')
                                                                ->where('ppi_spi_product_id', $product->id)
                                                                ->first();
                                           $checkStockInThisProduct = $Model('PpiSpiStatus')::checkPpiStatus($ppi_id, 'ppi_new_product_added_to_stock', ['ppi_spi_product_id' => $product->id])
                                        @endphp

                                        <tr style="background: {{ $checkExistingWithDB || $checkStockInThisProduct ? '#ffecb5' : null }}">
                                            <td>
                                                @if ($checkExistingWithDB)
                                                    @php
                                                        $barCodeDigit = $checkExistingWithDB->barcode;
                                                        $orginalBarCodeDigit = $checkExistingWithDB->original_barcode;
                                                    @endphp
                                                @else

                                                    <input class="mb-0 d-none" id="barcode_product_line_item"
                                                           type="checkbox" name="barcode_product_line_item[]"
                                                           {{ $ppiLastStatusCode == 'ppi_agreed_no_existing' ? 'checked' : null }}
                                                           value="{{$orginalBarCodeDigit}}"/>

                                                    <input class="mb-0 d-none" id=""
                                                           type="checkbox" name="barcode_product_unique_key[]"
                                                           {{ $ppiLastStatusCode == 'ppi_agreed_no_existing' ? 'checked' : null }}
                                                           value="{{$barCodeDigit}}"/>

                                                @endif
                                            </td>
                                            <!-- Product Name -->
                                            <td>
                                                {!! $Model('Product')::getColumn($product->product_id, 'name') !!}
                                            </td>
                                            <!-- End Product Name -->

                                            <!-- Product Qty -->
                                            <!-- If bundle -->
                                            @if($bundle_product)
                                                <td class="text-center">{{$bundle_product[$i]['bundle_name']}}</td>
                                                <input type="hidden" name="bundle_id[]"
                                                       value="{{ $bundle_product[$i]['id']  }}">
                                                @php $qty =  $bundle_product[$i]['bundle_size']; @endphp
                                                <td class="text-center">{{$qty}}</td>
                                            @else
                                                @php $qty =  $line_item_qty; @endphp
                                                <td class="text-center">{{$qty}}</td>
                                        @endif
                                        <!-- Bundle -->
                                            <input type="hidden" name="qty[]" value="{{$qty}}">
                                            <!-- End Produt Qty -->

                                            <td class="{{ !empty($checkExistingWithDB) ? 'unselectable' : null }}">
                                                @php
                                                    /**
                                                    * For Print
                                                    * */
                                                    $forPrint []= $Query::barcodeGenerator($barCodeDigit, ['show_digit_title' => $barCodeDigit, 'show_digit' => $orginalBarCodeDigit]);
                                                @endphp

                                                @if($barcode_format == 'Tag')
                                                    <p class="text-center">
                                                        {!! $Query::barcodeGenerator($barCodeDigit, ['show_digit' => $orginalBarCodeDigit]) !!}
                                                        {{--                                                            {{$barCodeDigit}}--}}
                                                    </p>
                                                @elseif($barcode_format == 'Bundle-Tag')
                                                    <p class="text-center">
                                                        {!! $Query::barcodeGenerator($barCodeDigit, ['show_digit' => $orginalBarCodeDigit]) !!}
                                                        {{--                                                            {{$barCodeDigit}}--}}
                                                    </p>
                                                @else
                                                    {{$barcode_format}}
                                                @endif

                                            </td>

                                            <td class="text-center">
                                                @if($checkExistingWithDB || $checkStockInThisProduct)
                                                    <span class="bg-success badge">
                                                            Stocked In
                                                        </span>
                                                @else
                                                    @if(auth()->user()->hasRoutePermission('ppi_ready_to_physical_validation_action'))
                                                        @if ($ppiLastStatusCode == 'ppi_agreed_no_dispute' ||  $ppiLastStatusCode == 'ppi_existing_product_added_to_stock')
                                                            @if($barcode_format == 'Tag' || $barcode_format == 'Bundle-Tag')
                                                                <button type="button" id=""
                                                                        class="py-0 btn-outline-info btn btn-sm existingProduct"
                                                                        data-barcode="{!! $barCodeDigit !!}"
                                                                        data-orginal_barcode="{!! $orginalBarCodeDigit !!}"
                                                                        data-ppi_product_id="{{ $ppi_product_id }}"
                                                                        data-product_unique_key="{{ $unique_key }}"
                                                                        data-product_qty="{{$qty}}"
                                                                        data-product_id="{!! $Query::accessModel('Product')::getColumn($product->product_id, 'id') !!}">
                                                                    Existing?
                                                                </button>
                                                            @endif <!-- Tag -->
                                                        @endif <!-- Agreed No Dispute -->
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endfor
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </form>
                @endif

            </div>
            <div class="col-md-4">
                <h6>
                    <div class="mb-0 px-2 title-with-border border-0 text-dark alert-secondary">
                        Information of the selected product
                    </div>
                </h6>
                <!--=====================================
                =========Product Information Table========
                =====================================--->
                <table class="table table-bordered table-sm table-thin">
                    <tbody>
                    @if (!empty($product))
                        @if ($set_product)
                            <tr>
                                <td width="150px">Name of Set</td>
                                <td>{{ $set_product }}</td>
                            </tr>
                        @endif
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
                                {!! $Query::accessModel('AttributeValue')::getValueById($productUnit) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>Product State</td>
                            <td> {!! $product->product_state !!} </td>
                        </tr>
                        <tr class="ppi_product_price_show">
                            <td>Price</td>
                            <td class="text-dark"> {!! $product->price !!} </td>
                        </tr>
                        <tr>
                            <td>Health Status</td>
                            <td class="text-dark"> {!! $product->health_status !!} </td>
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
//                    dump($product);
                    $checkStockInThisProduct = $Model('PpiSpiStatus')::checkPpiStatus($ppi_id, 'ppi_new_product_added_to_stock', ['ppi_spi_product_id' => $product->id]);
                @endphp

                @if($checkStockInThisProduct)
                    <div class="alert alert-success">This Product added to stock</div>
                @else
{{--                    @dump($ppiLastStatusCode)--}}
{{--                    @dump($ppiLastMainStatus)--}}
                    @if(auth()->user()->checkUserRoleTypeGlobal() || $ppiLastMainStatus->code == 'ppi_resent_to_wh_manager'  || $ppiLastMainStatus->code == 'ppi_sent_to_wh_manager' || $ppiLastMainStatus->code == 'ppi_dispute_by_wh_manager')

                            @if(auth()->user()->hasRoutePermission('ppi_ready_to_physical_validation_action'))

{{--                                @if ($checkThisProductIsDisputeNow == 'Dispute' || $checkThisProductIsDisputeNow == 'Correction'  && ($ppiLastMainStatus->code != 'ppi_resent_to_wh_manager'))--}}
                                @if ($checkThisProductIsDisputeNow == 'Dispute'  && ($ppiLastMainStatus->code != 'ppi_resent_to_wh_manager'))

                                @else
                                    <div class="mb-2 text-start">
                                        @if ($ppiLastStatusCode == 'ppi_agreed_no_existing')
                                            <div id="btnWrapperBarcodeStockIn" class="d-inline-block">

                                                @if($barcode_format == 'Tag' || $barcode_format ==  'Bundle-Tag')
                                                    <a class="py-0 btn btn-sm btn-primary" type="button" onclick="PrintDiv()">Start
                                                        to Print Barcode Tag</a>
                                                @endif

                                                <a href="#" id="printBarcodeStockIn" class="py-0 btn btn-sm btn-success">
                                                    Stock In
                                                </a>

                                            </div>
                                        @elseif ($ppiLastStatusCode == 'ppi_agreed_no_dispute' || $ppiLastStatusCode == 'ppi_existing_product_added_to_stock')
                                            <div style="font-size: 11px;">
                                                <input id="agreeNoExisting" type="checkbox" style="height: 12px;"/>
                                                <label for="agreeNoExisting">I agree that there are no existing.</label>
                                            </div>
                                            <div id="btnWrapperEx" class="d-inline-block">

                                            </div>
                                        @else
                                            @if(auth()->user()->hasRoutePermission('ppi_dispute_by_wh_manager_action'))
                                                <div style="font-size: 11px;">
                                                    <input id="agreeallok" type="checkbox" style="height: 12px;" checked="checked" />
                                                    <label for="agreeallok">I agree that there are no dispute.</label>
                                                    <small>আপনার কাজ কমাতে এখানে শুরুতে সেভ বাটন একটিভ আকারে দেয়া হয়েছে, Dispute দিতে দয়া করে নীল কালারের বাটন আনচেক করুন।</small>
                                                </div>

                                                <div id="btnWrapper" class="d-inline-block">

                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            @endif
                    @else
                        <div class="alert alert-danger">PPI is waiting for approval of Boss</div>
                    @endif
                @endif





            <!--==============================
            ======= Dispute Status ==========
            =============================-->
                <h6>
                    <div class="mb-0 px-2 title-with-border border-0 text-dark alert-secondary">
                        Dispute Status
                    </div>
                </h6>
                <table class="table table-bordered table-sm table-thin">
                    @php
                        $ppiDisputeCorrectionList = $Model('PpiSpiDispute')::ppiDisputeCorrectionList('Ppi', $product->id);
                    @endphp
                    <tbody>
                    <tr class="">
                        <td class="alert-danger"><strong>Dispute</strong></td>
                        <td class="alert-success"><strong>Correction</strong></td>
                    </tr>
                    @if(count((array)($ppiDisputeCorrectionList)) > 0)
                        @foreach ($ppiDisputeCorrectionList as $item)
                            <tr class="align-middle">
                                <td class="table-danger">
                                    {{ $item->dispute_note }} <br>
                                    Performed By {{ $Model('User')::getColumn($item->dispute_action_by, 'name') }} at
                                    {{ $item->dispute_date }}
                                </td>
                                <td class="table-success">
                                    @if ($item->correction_dispute_id)
                                        <i class="m-0 w-auto text-success fa fa-check-circle h3"
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
                    <tr>
                        <td colspan="2">
                            <!--@dump($ppiDisputeCorrectionList);-->
                            Dispute double click issue exists here!
                        </td>
                    </tr>
                    </tbody>
                </table>


            </div>
            <div class="col-md-2">
                <h6>
                    <div class="mb-0 px-2 title-with-border border-0 text-dark alert-secondary">
                        PPI ID : {{ $ppi_id }}
                    </div>
                </h6>
                @include('admin.pages.warehouse.single.ppi.ppi-status')
            </div>
        @endsection


        @section('cusjs')

            @php
                /**
                 * ppi Elements Setup
                 * Show / Hide or Any Permission use for Button , row a
                 */
                echo $PpiSpiPermission::elements();

                /** DisputeBtn Modal */

                $disputeRouteAction = route('ppi_dispute_by_wh_manager_action', [request()->get('warehouse_code'), $ppi_id, 'ppi_dispute_by_wh_manager']);
                $iAgreeThereAreNoDisputeRoute = route('ppi_agreed_no_dispute_action', [request()->get('warehouse_code'), $ppi_id, 'ppi_agreed_no_dispute']) . '?with-note=with ' . $product_name.'&&with-ppi_product_id='.$product->id;
                $iAgreeThereAreNoExistingRoute = route('ppi_agreed_no_existing_action', [request()->get('warehouse_code'), $ppi_id, 'ppi_agreed_no_existing']) . '?with-note=with ' . $product_name.'&&with-ppi_product_id='.$product->id;
            @endphp

            {{-- Dispute Modal --}}
            {!! $Component::bootstrapModal('disputeBtn', ['btnWrapperId' => 'btnWrapper', 'saveBtn' => false, 'backdrop' => true, 'formAction' => $disputeRouteAction]) !!}

            {{-- if Agree there are no Dispute Form Modal --}}
            {!! $Component::jsModal('noDisputeModal', ['btnWrapperId' => 'btnWrapper', 'formAction' => $iAgreeThereAreNoDisputeRoute, 'modalHeader' => 'Are you confirm', 'modalSubHeader' => 'There are no dispute products']) !!}

            {{-- if Agree there are no Existing Form Modal --}}
            {!! $Component::jsModal('noExistingModal', ['btnWrapperId' => 'btnWrapperEx', 'formAction' => $iAgreeThereAreNoExistingRoute, 'modalHeader' => 'Are you confirm', 'modalSubHeader' => 'There are no existing products']) !!}


            <!-- ==========================
                ======Dispute Form ===========
                =========================== -->

                <script type="text/template" id="disputeForm">
                    <input type="hidden" name="dispute_ele[{{ $product->id }}][ppi_product_id]"
                           value="{{ $product->id }}">
                    <input type="hidden" name="dispute_ele[{{ $product->id }}][action_format]" value="Dispute">
                    <div class="text-dark text-center form-check">
                        <input type="checkbox" id="{{ $product->id }}p_product" class="ms-3 me-0 w-auto checkItem"
                               name="dispute_ele[{{ $product->id }}][issue_column][product]" id="" value="product">
                        <label class="w-auto" for="{{ $product->id }}p_product">Product</label>
                        <input type="checkbox" id="{{ $product->id }}p_qty" class="ms-3 me-0 w-auto checkItem"
                               name="dispute_ele[{{ $product->id }}][issue_column][qty]" id="" value="qty">
                        <label class="w-auto" for="{{ $product->id }}p_qty">Quantity</label>
                    <!--
                        <input type="checkbox" id="{{ $product->id }}p_price" class="ms-3 me-0 w-auto checkItem" name="dispute_ele[{{ $product->id }}][issue_column][price]" value="price">
                        <label class="w-auto" for="{{ $product->id }}p_price">Price</label>
                        -->
                    </div>
                    <label class="w-auto" for="">Details of issue</label>
                    <textarea required class="form-control" name="dispute_ele[{{ $product->id }}][note]"
                              placeholder="Details of issue"></textarea>
                    <div class="gap-2 d-grid">
                        <button type="submit" onclick="return confirm('Are you sure want to dispute this item?');" class="mt-2 btn-outline-primary btn btn-sm">Submit</button>
                    </div>
                </script>


                <script>
                    jQuery(document).ready(function ($) {
                        /**
                         * ALl checkbox checked if clickall click
                         */
                        $('input#checkAllCheckBox').on('click', function () {
                            let ifThisChecked = $(this).prop('checked');
                            if (ifThisChecked) {
                                $('input#barcode_product_line_item').prop('checked', true)
                            } else {
                                $('input#barcode_product_line_item').prop('checked', false)
                            }
                        })


                        /**
                         * Button
                         * */
                            //If Do Dispute
                        let dispute = '<a id="disputeBtn" class="py-0 btn-outline-danger btn btn-sm">Dispute</a>';
                        //If I agreen there areo no dispute
                        let iAgreeThereAreNoDispute =
                            '<button type="button" id="noDisputeModal" class="py-0 btn btn-sm btn-primary" >Save</button>';

                        let iAgreeThereAreNoExisting =
                            '<button type="button" id="noExistingModal" class="py-0 btn btn-sm btn-primary" >Save</button>';
                        // //Print Barcode and Stock In
                        let printBarcodeStockIn =
                            '<a href="#" id="printBarcodeStockIn" class="py-0 btn-outline-primary btn btn-sm">Print Barcode and Stock In</a>';

                        /** I agree There are no dispute Input Box Action*/
                        $('input#agreeallok').click(function () {
                            let chekedIn = $(this).prop('checked');
                            if (chekedIn) {
                                //$('#btnWrapper').empty().html(printBarcodeStockIn)
                                $('#btnWrapper').empty().html(iAgreeThereAreNoDispute)
                            } else {
                                disputeBtnLoaded();
                            }
                        })

                        /** Dispute form Append on Dispute Modal When Press On Dispute Button*/
                        //Function for dispute button
                        function disputeBtnLoaded() {
                            $('#btnWrapper').empty().html(dispute);
                        }

                        disputeBtnLoaded();

                        $('#disputeBtnModalBody').empty().html($('script#disputeForm').html());


                        /** I agree There are no Existing Input Box Action*/
                        $(document).on('click', 'input#agreeNoExisting', function () {
                            let chekedIn = $(this).prop('checked');
                            if (chekedIn) {
                                $('#btnWrapperEx').empty().html(iAgreeThereAreNoExisting)
                            } else {
                                $('#btnWrapperEx').empty()
                            }
                        })


                        //print Barcode and Stock In Action

                        $('div#btnWrapperBarcodeStockIn').on('click', 'a#printBarcodeStockIn', function (e) {
                            e.preventDefault();
                            let barcodeRoute =
                                "{{ route('ppi_product_stock_in', [request()->get('warehouse_code')]) }}";
                            let stockInForm = 'form#ppiFormAction';
                            $(stockInForm).attr('action', barcodeRoute)
                            let productItemCheck = $('input#barcode_product_line_item');
                            let checkLength = $(productItemCheck).is(':checked');

                            confirmAlert('Are you ready to stock in product', '', '#ppiFormAction');
                            //$(stockInForm).submit();
                            //alert(checkLength);
                            //if (checkLength > 0) {
                            // $(stockInForm).submit();
                            //} else {
                            // alert('You have to select at least one item')
                            //}
                            //alert(barcodeRoute)
                        })


                    })
                </script>





                <!--==============================
                =====================================
                Existing Product Check Modal
                =================================-->

                <div id="reload_modal">
                    <?php echo $Component::bootstrapModal('existingProduct', ['modalHeader' => 'Scan Barcode', 'position' => 'right', 'backdrop' => true, 'saveBtn' => false, 'use' => 'class']); ?>
                </div>


                <script>
                    /**
                     * Existing Button Action
                     * */
                    $(document).on('click', 'button.existingProduct', function (e) {
                        e.preventDefault();
                        // alert('ok')
                        let getThisBarcode = $(this).data('barcode');
                        let getThisOrginalBarcode = $(this).data('orginal_barcode');
                        let getThisProductId = $(this).data('product_id');
                        let getThisPpiProductId = $(this).data('ppi_product_id');
                        let getThisProductUniqueKey = $(this).data('product_unique_key');
                        let getThisProductQty = $(this).data('product_qty');

                        let ExistingModalBody = '#reload_modal #existingProductModalBody';
                        let ExistingModalHtml = `
                                <div class="existingProductModalWrap">
                                        <div class="form-group">
                                            <label>Click On Input box before scan Barcode</label>
                                            <input type="text" class="form-control form-control-sm" value="" id="existingProductBarcode">
                                        </div>
                                        <div class="orginalBarcodeShow">

                                        </div>
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisBarcode}" id="existingPpiProductHiddenBarcode">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisOrginalBarcode}" id="existingPpiProductHiddenOrginalBarcode">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisPpiProductId}" id="existingPpiProductId">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisProductQty}" id="existingPpiProductQty">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisProductId}" id="existingProductId">
                                        <input type="hidden" class="form-control form-control-sm" value="${getThisProductUniqueKey}" id="existingProductUniqueKey">
                                        <div class="saveBtnForExistingProductStockIn">

                                        <div>
                                </div>
                        `;

                        $(ExistingModalBody).html(ExistingModalHtml);
                        let BarcodeInputTextField = ExistingModalBody + ' input#existingProductBarcode';
                        //$(BarcodeInputTextField).val(getThisBarcode);
                        $(BarcodeInputTextField).val();
                        //$(BarcodeInputTextField).val(getThisBarcode);
                        //$('#reload_me').html();
                    });


                    let barcodeInputField = '#existingProductModalBody  input#existingProductBarcode';
                    let barcodeInputHiddenBarcodeField = '#existingProductModalBody  input#existingPpiProductHiddenBarcode';
                    let barcodeInputHiddenOrginalBarcodeField = '#existingProductModalBody  input#existingPpiProductHiddenOrginalBarcode';
                    let productIdInputField = '#existingProductModalBody  input#existingProductId';
                    let productQtyInputField = '#existingProductModalBody  input#existingProductQty';
                    let ppiProductIdInputField = '#existingProductModalBody  input#existingPpiProductId';
                    let ppiProductUniquekeyInputField = '#existingProductModalBody  input#existingProductUniqueKey';


                    //Existing Product Stock In Function
                    function existingStock(hiddenBarcode) {
                        let barcode = $(barcodeInputField).val();
                        let barcodeHidden = $(barcodeInputHiddenBarcodeField).val();
                        let barcodeHiddenOrginal = $(barcodeInputHiddenOrginalBarcodeField).val();
                        let productId = $(productIdInputField).val();
                        let ppiProductId = $(ppiProductIdInputField).val();
                        let ppiProductQty = $(productQtyInputField).val();
                        let productUniqueKey = $(ppiProductUniquekeyInputField).val();
                        //alert(hiddenBarcode)
                        $('.existingProductOpenModal').modal("hide");
                        // if (barcode == barcodeHidden) {
                        if (hiddenBarcode) {
                            $.ajax({
                                url: `{{ route('ppi_existing_product_check_during_stock', request()->get('warehouse_code')) }}`,
                                type: 'POST',
                                //dataType: 'json',
                                data: {
                                    '_token': '{{ csrf_token() }}',
                                    'ppi_id': '{{ $ppi_id }}',
                                    //'barcode': barcode,
                                    'barcode': hiddenBarcode,
                                    'product_qty': ppiProductQty,
                                    //'orginal_barcode': barcodeHiddenOrginal,
                                    //'product_unique_key': productUniqueKey,
                                    'replace_with_barcode': barcodeHiddenOrginal,
                                    'product_id': productId,
                                    'ppi_product_id': ppiProductId
                                },
                                success: function (response) {
                                    //$("#existingProductOpenModal .orginalBarcodeShow").empty().html($(barcodeInputHiddenOrginalBarcodeField).val())
                                    console.log(response);
                                    if (response.status == 1) {
                                        toastr.success(response.message);
                                    }
                                    if (response == false) {
                                        //toastr.error('This is product was not found in the Database as Existing');
                                        alert(`<h5>${hiddenBarcode}</h5> This product was not found in the Database as Existing`)
                                    }

                                    $("#reload_wrap").load(location.href + " #reload_wrap > *");
                                },
                            });
                        } else {
                            alert(`<h5>${barcodeHidden}</h5>  You have selected wrong item`)
                        }
                    }

                    /** Barcode reader Apply In Input Field */
                    // $('#reload_modal').bind('keydown paste', barcodeInputField, function () {
                    //     //alert($(this).val());
                    //     let html = '<button type="button" class="mt-2 btn btn-primary btn-sm">Submit</button>';
                    //     $('#reload_modal .saveBtnForExistingProductStockIn').html(html);
                    // })

                    //Action if press enter key
                    $(document).on('click', '#existingProductOpenModal .saveBtnForExistingProductStockIn button', function (e) {
                        e.preventDefault()
                        let thisDataInputVal = $(this).data('input_val')
                        //alert(thisDataInputVal)
                        existingStock(thisDataInputVal);
                    })
                    //Action if click on submit button
                    //$('#existingProductOpenModal').one('keypress', barcodeInputField,  function(e){
                    $(document).on('paste keypress', '#existingProductOpenModal ' + barcodeInputField, function (e) {
                        let key = e.which;

                        let element = this;
                        let userEnteredText;
                        setTimeout(function () {
                            let userEnteredText = $(element).val();
                            // alert(userEnteredText);
                            let html = `<button type="button" class="mt-2 btn btn-primary btn-sm" data-input_val="${userEnteredText}">Submit</button>`;
                            $('#reload_modal .saveBtnForExistingProductStockIn').html(html);
                            if (key == 13) {
                                // alert(userEnteredText)
                                existingStock(userEnteredText);
                            }
                        }, 5); //html5 min is 4ms.

                    })
                </script>

                <?php
                //For Barcode print Modal
                $modalPrintBtn = '<a class="btn btn-sm btn-primary" type="button" onclick="PrintDiv()">Print</a>';
                echo $Component::bootstrapModal('barcodeForPrint', ['saveBtn' => false, 'modalSize' => 'md', 'modalHeader' => $modalPrintBtn]);
                ?>

                <script>
                    //Show barcode in Print Modal
                    function forPrintModal() {

                        let html = `
                            <div id="printBody" style="margin: 0 auto; width: 288px;">
                                <table style="width: 288px; text-align: center; margin: 0 auto;">
                                    @foreach($forPrint as $key => $data)
                        @php
                            if ($key % 2 == 0) {
                                echo '<tr>';
                            }

                            echo '<td>';
                                echo $data;
                            echo '</td>';

                            if ($key % 2 == 1) {
                                echo '</tr><tr><td>&nbsp;</td> </tr><tr><td>&nbsp;</td> </tr>';
                            }
                        @endphp
                        @endforeach
                        </table>
                    </div>
`;
                        return html;
                    }

                    $('#barcodeForPrintModalBody').empty().append(forPrintModal());


                    //
                    // Print Button Action
                    function PrintDiv() {
                        var divToPrint = document.getElementById('printBody');
                        var popupWin = window.open('', '_blank', 'width=700,height=700');
                        popupWin.document.open();
                        popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
                        popupWin.document.close();
                    }
                </script>

@endsection
