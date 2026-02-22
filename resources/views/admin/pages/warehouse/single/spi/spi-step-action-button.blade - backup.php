@if($spi->transferable)
    @php
        $spiComplete = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id)->code;
    @endphp
        @if($spiComplete ==  'spi_all_steps_complete')
           @php
              $doneThisAction =  true;
              $doneThisActionButton = true;
           @endphp
        @else
            @include('admin.pages.warehouse.single.spi.spi-transfer-btn')
            @php
                $doneThisAction = doneThisAction();
                $doneThisActionButton = doneThisActionButton();
            @endphp
       @endif
@else
    <div class="d-inline-block ms-2" id="ppi_action">
    <form action="" method="post" id="ppi_action_form">
        @csrf
        <!-- ====================================================
           # PPI Sent To Boss
           # PPI Correction(If Boss Dispute)
           # Direct PPI Sent To Warehouse Manager (If Boss Dispute)
        ========================================================= -->
        @if($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_boss_action'))
            @php
                $ppiStatusFormAction =  route('spi_sent_to_boss_action', [$warehouse_code, $spi->id, 'spi_sent_to_boss']);
                $ppiStatusFormBtnText = 'Sent to Boss';
                $ppiStatusFormBtnClass = 'indigo';
                $doneThisAction = $Model('PpiSpiStatus')::checkSpiStatus($spi->id, 'spi_sent_to_boss');
                if($doneThisAction === true){
                     $doneThisActionButton = true;
                }
            @endphp

        <!-- ===============================
           # Boss Can Correction on Dispute Product
           # Boss Sent to Warehouse Manager
        ==================================== -->
        @elseif($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_wh_manager_action'))
            @php
                //echo $checkPpiLastSts->code;
                //$checkPpiLastSts->code == 'ppi_sent_to_boss'
                if($checkSpiLastMainSts->code == 'spi_sent_to_boss'){
                    $ppiStatusFormAction =  route('spi_sent_to_wh_manager_action', [$warehouse_code, $spi->id, 'spi_sent_to_wh_manager']);
                    $ppiStatusFormBtnText = 'Sent to Warehouse Manager';
                    $ppiStatusFormBtnClass = 'indigo';
                    $doneThisAction =   false;
                    $doneThisActionButton = false;
                }elseif($checkSpiLastMainSts->code  == 'spi_dispute_by_wh_manager'){
                    $ppiStatusFormAction =  route('spi_resent_to_wh_manager_action', [$warehouse_code, $spi->id, 'spi_resent_to_wh_manager']);
                    $ppiStatusFormBtnText = 'Re Sent to Warehouse Manager';
                    $ppiStatusFormBtnClass = 'teal';
                    $doneThisAction =   false;
                    $doneThisActionButton = false;
                }
                elseif($checkSpiLastMainSts->code  == 'spi_sent_to_wh_manager'){
                    $doneThisAction =  true;
                    $doneThisActionButton = true;
                }elseif($checkSpiLastMainSts->code  == 'spi_correction_done_by_boss'){
                    $ppiStatusFormAction =  route('spi_resent_to_wh_manager_action', [$warehouse_code, $spi->id, 'spi_resent_to_wh_manager']);
                    $ppiStatusFormBtnText = 'Re Sent to Warehouse Manager';
                    $ppiStatusFormBtnClass = 'teal';
                    $doneThisAction = false;
                    $doneThisActionButton = true;
                } elseif($checkSpiLastMainSts->code  == 'spi_dispute_by_wh_manager'){
                    $doneThisAction =  false;
                    $doneThisActionButton = false;
                }elseif($checkSpiLastMainSts->code  == 'spi_resent_to_wh_manager'){
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                }else {
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                }
            @endphp



        <!--==================================
            # Warehouse Manager Can Dispute
        ====================================-->
        @elseif($generalUser && auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action'))
            @php
                if($singleProductValidationDone){
                    $ppiStatusFormAction =  route('spi_all_steps_complete_action', [$warehouse_code, $spi->id, 'spi_all_steps_complete']);
                    $ppiStatusFormBtnText = 'Close this SPI';
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
              $spiComplete = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id)->code;
              if($spiComplete ==  'spi_all_steps_complete'){
                  $doneThisAction =  true;
                  $doneThisActionButton = true;
              }
        @endphp



        @php
            $globalUser =  auth()->user()->checkUserRoleTypeGlobal();
            if($globalUser && $singleProductValidationDone  && $spiComplete != 'spi_all_steps_complete'){
               $ppiStatusFormAction =  route('spi_all_steps_complete_action', [$warehouse_code, $spi->id, 'spi_all_steps_complete']);
                $ppiStatusFormBtnText = 'Close this SPI';
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
                    data-url = "{{$ppiStatusFormAction}}"
                    class="btn btn-md btn-{{$ppiStatusFormBtnClass}} text-white py-0"> {{$ppiStatusFormBtnText}} </button>

                {!!
                    $Component::confirmModal('ppiActionModal', 'form#ppi_action_form', 'Are you sure?', '', '')
                !!}
                <?php /*
                @if(isset($disputeRoute))
                <button type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#ppiActionModal"
                    data-url = "{{$disputeRoute}}"
                    class="btn btn-sm btn-danger text-white py-0"> Dispute </button>

                {!!
                    $Component::confirmModal('ppiActionModal', 'form#ppi_action_form', 'Are you sure?', '', '')
                !!}
                @endif
                */ ?>

            @endif
        @endisset

    </form>
</div>

@endif

@section('bottomjs')
    @parent
   <?php
   /**
    * ppi Elements Setup
    * Show / Hide or ANy Permission use for Button , row a
    */
    echo $PpiSpiPermission::elements();
   ?>

    @if(isset($doneThisAction) && $doneThisAction === true)
        <script>
            $('#spi_content .done_this_action').remove();
        </script>
    @endif

    @if(isset($doneThisActionButton) && $doneThisActionButton === true)
        <script>
            $('#spi_content .done_this_action_btn').remove();
        </script>
    @endif

    <script>
        $('#ppi_action button').on('click', function(){
            // alert(1)
            let Url = $(this).attr('data-url');
            $('#ppi_action form#ppi_action_form').prop('action', Url);
        })
        $('#ppiActionModal').on('click', 'button.modal-cancel', function(){
            $('#ppi_action form#ppi_action_form').prop('action', '');
        })
    </script>


@endsection
