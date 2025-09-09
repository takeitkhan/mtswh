<div class="d-inline-block ms-2" id="ppi_action">
    <form action="" method="POST" id="ppi_action_form">
    @csrf


    <!-- ====================================================
           # Subordinate Manager Section
           # PPI Sent To Boss
           # PPI Correction(If Boss Dispute)
           # Direct PPI Sent To Warehouse Manager (If Boss Dispute)
        ========================================================= -->
    @if($generalUser && auth()->user()->hasRoutePermission('ppi_sent_to_boss_action'))
        @php

            $ppiStatusFormAction =  route('ppi_sent_to_boss_action', [$warehouse_code, $ppi->id, 'ppi_sent_to_boss']);
            $ppiStatusFormBtnText = 'Sent to Boss';
            $ppiStatusFormBtnClass = 'indigo';
            $doneThisAction = $Model('PpiSpiStatus')::checkPpiStatus($ppi->id, 'ppi_sent_to_boss');
            if($doneThisAction){
                $doneThisActionButton = true;
            }
        @endphp


        <!-- ===============================
            # Boss Section
           # Boss Can Correction on Dispute Product
           # Boss can Sent to Warehouse Manager
        ==================================== -->
    @elseif($generalUser && auth()->user()->hasRoutePermission('ppi_sent_to_wh_manager_action'))
        @php
            if($checkPpiLastSts->code == 'ppi_sent_to_boss') {
                $ppiStatusFormAction =  route('ppi_sent_to_wh_manager_action', [$warehouse_code, $ppi->id, 'ppi_sent_to_wh_manager']);
                $ppiStatusFormBtnText = 'Sent to Warehouse Manager';
                $ppiStatusFormBtnClass = 'indigo';
                $doneThisAction =   false;
                $doneThisActionButton = false;
            } elseif($checkPpiLastSts->code  == 'ppi_sent_to_wh_manager'){
                $doneThisAction =   true;
                $doneThisActionButton = true;
            } elseif($checkPpiLastSts->code  == 'ppi_correction_done_by_boss') {
                $ppiStatusFormAction =  route('ppi_resent_to_wh_manager_action', [$warehouse_code, $ppi->id, 'ppi_resent_to_wh_manager']);
                $ppiStatusFormBtnText = 'Re Sent to Warehouse Manager';
                $ppiStatusFormBtnClass = 'teal';
                $doneThisAction = false;
                $doneThisActionButton = true;
            }elseif($checkPpiLastSts->code  == 'ppi_dispute_by_wh_manager'){
                $doneThisAction =  true;
                $doneThisActionButton = true;
            }
            elseif($checkPpiLastSts->code  == 'ppi_resent_to_wh_manager'){
                $doneThisAction =   true;
                $doneThisActionButton = true;
            }else {
                $doneThisAction =   true;
                $doneThisActionButton = true;
            }



        @endphp



        <!--==================================
            # Warehouse Manager  Section
            # He Can Dispute
            # He Can Check Existing product
            # He can Print Barcode
            # He cam stock in Product
        ====================================-->
        @elseif($generalUser && auth()->user()->hasRoutePermission('ppi_dispute_by_wh_manager_action'))
            @php
                if($singleProductValidationDone && $setProductValidationDone){
                    $ppiStatusFormAction =  route('ppi_all_steps_complete_action', [$warehouse_code, $ppi->id, 'ppi_all_steps_complete']);
                    $ppiStatusFormBtnText = 'Close this PPI';
                    $ppiStatusFormBtnClass = 'indigo';
                    $doneThisAction =   false;
                    $doneThisActionButton = true;
                }else {
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                }
            @endphp

        @endif


        @php
            $ppiComplete = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id)->code;
            if($ppiComplete == 'ppi_all_steps_complete'){
                $doneThisAction =  true;
                $doneThisActionButton = true;
            }
        @endphp




        @php
            $globalUser =  auth()->user()->checkUserRoleTypeGlobal();
            if($globalUser && $singleProductValidationDone && $setProductValidationDone && $ppiComplete != 'ppi_all_steps_complete'){
                $ppiStatusFormAction =  route('ppi_all_steps_complete_action', [$warehouse_code, $ppi->id, 'ppi_all_steps_complete']);
                $ppiStatusFormBtnText = 'Close this PPI';
                $ppiStatusFormBtnClass = 'indigo';
                $doneThisAction =  false;
                $doneThisActionButton = false;
            }elseif($globalUser) {
                $doneThisAction = false;
                $doneThisActionButton = false;
            }
        @endphp








    <!-- Button For Action -->
        @isset($ppiStatusFormAction)
            @if(isset($doneThisAction) && $doneThisAction == false)
                <button type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#ppiActionModal"
                        data-url="{{$ppiStatusFormAction}}"
                        class="btn btn-md btn-{{$ppiStatusFormBtnClass}} text-white py-0"> {{$ppiStatusFormBtnText}} </button>

                {!!
                    $Component::confirmModal('ppiActionModal', 'form#ppi_action_form', 'Are you sure?', '', '')
                !!}

            @endif
        @endisset

    </form>
</div>


@section('bottomjs')
    @parent
    <?php
    /**
     * ppi Elements Setup
     * Show / Hide or ANy Permission use for Button , row a
     */
    //echo $PpiSpiPermission::elements();
    ?>

    @if(isset($doneThisAction) && $doneThisAction === true)
        <script>
            $('#ppi_content .done_this_action').remove();
        </script>
    @endif

    @if(isset($doneThisActionButton) && $doneThisActionButton === true)
        <script>
            $('#ppi_content .done_this_action_btn').remove();
        </script>
    @endif



    <script>
        $('#ppi_action button').on('click', function () {
            // alert(1)
            let Url = $(this).attr('data-url');
            $('#ppi_action form#ppi_action_form').prop('action', Url);
        })
        $('#ppiActionModal').on('click', 'button.modal-cancel', function () {
            $('#ppi_action form#ppi_action_form').prop('action', '');
        })
    </script>

@endsection
