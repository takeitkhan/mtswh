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
                // Check if Delivery Challan has been generated
                $challanGenerated = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi->id)
                                                          ->where('code', 'spi_delivery_challan_generated')
                                                          ->exists();
                
                if($challanGenerated){
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
            // Check if Delivery Challan has been generated for global user too
            $challanGenerated = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi->id)
                                                      ->where('code', 'spi_delivery_challan_generated')
                                                      ->exists();
            
            if($globalUser && $challanGenerated && $spiComplete != 'spi_all_steps_complete'){
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

        <!-- Generate Delivery Challan PDF Button (Before Close) -->
        @if($spiComplete != 'spi_all_steps_complete')
            <div class="mt-3 mb-3">
                <button type="button"
                        id="generateChallanBtn"
                        data-warehouse="{{ $warehouse_code }}"
                        data-spi-id="{{ $spi->id }}"
                        data-status="spi_delivery_challan_generated"
                        class="btn btn-md btn-warning text-white py-1 w-100">
                    <i class="fa fa-file-pdf me-1"></i> Generate Delivery Challan PDF
                </button>
            </div>
        @endif



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

    <!-- SPI Delivery Challan PDF Generation Script -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const btn = document.getElementById('generateChallanBtn');
    
        if (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
    
                let warehouse = this.dataset.warehouse;
                let spiId     = this.dataset.spiId;
                let status    = this.dataset.status;
                
                console.log('Challan button clicked:', {warehouse, spiId, status});
                
                // Disable button to prevent double click
                this.disabled = true;
                this.classList.add('disabled');
                
                // 1. Open PDF in new tab (GET route)
                window.open(`/spi/${warehouse}/${spiId}/${status}/challan/pdf`, "_blank");
                
                // 2. Mark status (POST route)
                const postUrl = `/spi/${warehouse}/${spiId}/${status}/challan-pdf`;
                console.log('Posting to:', postUrl);
                
                fetch(postUrl, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({})
                })
                .then(res => {
                    console.log('Response status:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        console.log('Success! Reloading page...');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                        this.disabled = false;
                        this.classList.remove('disabled');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error: ' + error.message);
                    this.disabled = false;
                    this.classList.remove('disabled');
                });
            });
        }
    });

    </script>

@endsection
