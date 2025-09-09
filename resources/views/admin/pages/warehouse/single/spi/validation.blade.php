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
                                    @include('admin.pages.warehouse.single.spi.validation-product-table')
                                @endif
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
                        let dispute = '<a id="disputeBtn" class="btn btn-sm btn-outline-danger py-0">Dispute</a>';
                        //If I agreen there areo no dispute
                        let iAgreeThereAreNoDispute =
                            '<button type="button" id="noDisputeModal" class="btn btn-sm btn-primary py-0" >Save</button>';

                        let iAgreeThereAreNoExisting =
                            '<button type="button" id="noExistingModal" class="btn btn-sm btn-primary py-0" >Save</button>';
                        // //Print Barcode and Stock In
                        let printBarcodeStockIn =
                            '<a href="#" id="printBarcodeStockIn" class="btn btn-sm btn-outline-primary py-0">Print Barcode and Stock In</a>';

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
                                "{{ route('spi_product_stock_out', [request()->get('warehouse_code')]) }}";
                            let stockInForm = 'form#ppiFormAction';
                            $(stockInForm).attr('action', barcodeRoute)
                            let productItemCheck = $('input#barcode_product_line_item');
                            let checkLength = $(productItemCheck).is(':checked');
                            // alert(checkLength)
                            confirmAlert('Are you ready to stock out the product', '', '#ppiFormAction');
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


    <?php /*



    <!--==============================
    =====================================
    Existing Product Check Modal
    =================================-->



    <div id="reload_modal">
        <?php echo $Component::bootstrapModal('existingProduct', ['modalHeader' => 'Scan Barcode', 'position' => 'right', 'backdrop' => true, 'saveBtn' => false, 'use' => 'class']); ?>
    </div>


    <script>
        //Existing Button Action

        //$('button.existingProduct').click(function(){
        $(document).on('click', 'button.existingProduct', function(e) {
            e.preventDefault();
            // alert('ok')
            let getThisBarcode = $(this).data('barcode');
            let getThisOrginalBarcode = $(this).data('orginal_barcode');
            let getThisProductId = $(this).data('product_id');
            let getThisPpiProductId = $(this).data('spi_product_id');
            let getThisProductUniqueKey = $(this).data('product_unique_key');

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
        let ppiProductIdInputField = '#existingProductModalBody  input#existingPpiProductId';
        let ppiProductUniquekeyInputField = '#existingProductModalBody  input#existingProductUniqueKey';


        // Barcode reader Apply In Input Field
        $('#reload_modal').bind('keydown paste', barcodeInputField, function() {
            //alert($(this).val());
            let html = '<button type="button" class="btn btn-primary btn-sm mt-2">Submit</button>';
            $('#reload_modal .saveBtnForExistingProductStockIn').html(html);
        })

        //Existing Product Stock In Function
        function existingStock()  {
            let barcode = $(barcodeInputField).val();
            let barcodeHidden = $(barcodeInputHiddenBarcodeField).val();
            let barcodeHiddenOrginal = $(barcodeInputHiddenOrginalBarcodeField).val();
            let productId = $(productIdInputField).val();
            let ppiProductId = $(ppiProductIdInputField).val();
            let productUniqueKey = $(ppiProductUniquekeyInputField).val();
            //alert(barcodeHiddenOrginal)
            $('.existingProductOpenModal').modal("hide");
            if(barcode == barcodeHidden){
                $.ajax({
                    url: `{{ route('ppi_existing_product_check_during_stock', request()->get('warehouse_code')) }}`,
                    //?barcode=${barcode}&&product_id=${productId}&&ppi_product_id=${ppiProductId}
                    type: 'POST',
                    //dataType: 'json',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'ppi_id': '{{ $spi_id }}',
                        'barcode': barcode,
                        'orginal_barcode' : barcodeHiddenOrginal,
                        'product_unique_key' : productUniqueKey,
                        'product_id': productId,
                        'spi_product_id': ppiProductId
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status == 1) {
                            toastr.success(response.message);
                        }
                        if (response == false) {
                            //toastr.error('This is product was not found in the Database as Existing');
                            alert(`<h5>${barcodeHidden}</h5> This product was not found in the Database as Existing`)
                        }

                        $("#reload_wrap").load(location.href + " #reload_wrap > *");
                    },
                });
            }else {
                alert(`<h5>${barcodeHidden}</h5>  You have selected wrong item`)
            }
        }

        //$("#reload_wrap").load(location.href + " #reload_wrap");
        //$(document).('load', '#reload_wrap', location.href + " #reload_wrap");


        //Action if press enter key
        //$('#existingProductOpenModal').one('click', '.saveBtnForExistingProductStockIn button', function(e){
        $(document).on('click', '#existingProductOpenModal .saveBtnForExistingProductStockIn button', function(e) {
            e.preventDefault()
            existingStock();
        })
        //Action if click on submit button
        //$('#existingProductOpenModal').one('keypress', barcodeInputField,  function(e){
        $(document).on('keypress paste', '#existingProductOpenModal ' + barcodeInputField, function(e) {
            //alert('ok')
            let key = e.which;
            $("#existingProductOpenModal .orginalBarcodeShow").empty().html($(barcodeInputHiddenBarcodeField).val())
            //console.log(key);
            if (key == 13) {
                existingStock();
            }
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
       var popupWin = window.open('', '_blank', 'width=300,height=300');
            popupWin.document.open();
            popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
        popupWin.document.close();
    }
</script>

 */ ?>
@endsection
