<div class="row" id="tbl_ppi_product">
    <div class="col-md-12 table-wrapper desktop-view mobile-view h-auto">

        <form action="javascript:void(0)" method="post" id="tbl_ppi_product_form_action">
            @csrf
            <h6>
                <div class="title-with-border">
                    <span>Products</span>
                    <span class="done_this_action">
                        <!-- Create Set Button -->

                        <!-- ENd Set product -->


                        <!-- Dispute Button -->
                        @if($generalUser && auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action'))
                            @php
                                if(isset($checkSpiLastMainSts) && in_array($checkSpiLastMainSts->code, ['spi_sent_to_wh_manager','spi_resent_to_wh_manager'])){
                                    $disputeRoute = route('spi_dispute_by_wh_manager_action', [$warehouse_code, $spi->id, 'spi_dispute_by_wh_manager']);
                                    $dispute = $Model('PpiSpiStatus')::checkSpiStatus($spi->id, 'spi_dispute_by_wh_manager');
                                    //dump($dispute);
                                }
                            @endphp
                        <!-- Correction Button -->
                        @elseif ($generalUser && auth()->user()->hasRoutePermission('spi_product_info_correction_by_boss_action'))
                            @php
                               if(isset($checkSpiLastMainSts) && $checkSpiLastMainSts->code == 'spi_dispute_by_wh_manager'){
                                   //echo 'ok';
                                $correctionRoute = route('spi_product_info_correction_by_boss_action', [$warehouse_code, $spi->id, 'spi_product_info_correction_by_boss']);
                               }
                            @endphp
                        @endif
                    </span>

                    @if(isset($disputeRoute))
                        {{-- <button type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#dsiputeButton"
                            data-url = "{{$disputeRoute}}"
                            name="dispute_button" id="dispute_button" class="btn btn-sm btn-danger py-0" disabled>Dispute
                        </button>
                        {!!
                            $Component::confirmModal('dsiputeButton', 'form#tbl_ppi_product_form_action', 'Are you sure to Dispute?', '', '')
                        !!} --}}
                    @endif
                    <!-- End Dispute Button -->
                    @php
                       global $physicalValidate;
                       if(isset( $physicalValidate)){
                        $physicalValidateRoute = '';
                       }
                    @endphp

                </div>
            </h6>
            <div id="ppi_product_wrap">
            </div>


        </form>
          <!-- ======================
            ==== PPi Set Product ======
            ======================= -->
            <script type="teaxt/template" id="table_ppi_product">

                <!-- End Ppi Set Product -->
                <table class="" style="border-collapse: collapse;">
                    @php
                        $vc = 0;
                        $vr = 0;
                    @endphp
{{--                    @include('admin.pages.warehouse.single.ppi.table-set-product')--}}
                    <thead>
                        <tr style="background: #d1f4ff !important;">
                            <th class="not_print"></th>
                            <th class="not_print">
                                @if(isset($disputeRoute) || isset($correctionRoute) ||  isset($physicalValidateRoute) )
                                    Action
                                @endif
                            </th>
                            <th>Product Name</th>
                            <th>QTY</th>
                            <th>Unit</th>
                            <th class="ppi_product_price_show not_print">Price</th>
                            <th>Product State</th>
                            <th>Health Status</th>
                            <th>Barcode Format</th>
                            <th class="not_print">Note</th>
                            <th class="">From Warehouse</th>
                            <th class="not_print">Dispute Note</th>
                            <th width="135px" class="not_print">Physical Validation</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr class="d-none">
                            <td><form></form></td>
                        </tr>

                        <!--==========================
                        ===== PPi Single Product=====
                        $getPpiProduct > Its defines in PPI Form.blade.php
                        ============================-->

                        @include('admin.pages.warehouse.single.spi.table-single-product')
                        <!-- End Ppi Single Product -->

                    </tbody>
                </table>

            </script>
    </div>
</div>

@section('cusjs')
    @parent

    <style>
        .fieldset {
            border: 1px solid #9faec1;
            background: #F8F8F8;
            border-radius: 0px;
            padding: 2px 0px;
        }

        .fieldset legend {
            background: #1F497D;
            color: #fff;
            padding: 0px 5px ;
            font-size: 12px;
            border-radius: 5px;
            margin-left: 20px;
        }
        legend {
            float: unset;
            width: unset;
        }
    </style>
    <script>
        $('form#tbl_ppi_product_form_action #ppi_product_wrap').html($('#table_ppi_product').html())
        {{--checkForSetProduct('{{ route('ppi_set_product_store', $warehouse_code) }}');--}}
    </script>


    <style>
        .table-wrapper table tbody td {
            border: 1px solid #ddd;
        }
    </style>
@endsection




