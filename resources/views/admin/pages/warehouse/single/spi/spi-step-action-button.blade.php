@if($spi->transferable)
    @php
        $spiComplete = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id)->code;
        $challanGenerated = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi->id)
                                                  ->where('code', 'spi_delivery_challan_generated')
                                                  ->exists();
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
        
        @php
            // Initialize all variables
            $ppiStatusFormAction = null;
            $ppiStatusFormBtnText = null;
            $ppiStatusFormBtnClass = null;
            $doneThisAction = true;
            $doneThisActionButton = true;
            
            $generalUser = auth()->user()->checkUserRoleTypeGeneral();
            $globalUser = auth()->user()->checkUserRoleTypeGlobal();
            $spiComplete = $Model('PpiSpiStatus')::getSpiLastStatus($spi->id)->code;
            $challanGenerated = $Model('PpiSpiStatus')::where('ppi_spi_id', $spi->id)
                                                      ->where('code', 'spi_delivery_challan_generated')
                                                      ->exists();
        @endphp
        
        <!-- If SPI is already completed -->
        @if($spiComplete == 'spi_all_steps_complete')
            @php
                $doneThisAction = true;
                $doneThisActionButton = true;
            @endphp
        <!-- Warehouse Manager can Close SPI only after Challan Generated -->
        @elseif($generalUser && auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action') && $challanGenerated)
            @php
                $ppiStatusFormAction = route('spi_all_steps_complete_action', [$warehouse_code, $spi->id, 'spi_all_steps_complete']);
                $ppiStatusFormBtnText = 'Close this SPI';
                $ppiStatusFormBtnClass = 'indigo';
                $doneThisAction = false;
                $doneThisActionButton = true;
            @endphp
        <!-- Global User can Close SPI only after Challan Generated -->
        @elseif($globalUser && $challanGenerated && $spiComplete != 'spi_all_steps_complete')
            @php
                $ppiStatusFormAction = route('spi_all_steps_complete_action', [$warehouse_code, $spi->id, 'spi_all_steps_complete']);
                $ppiStatusFormBtnText = 'Close this SPI';
                $ppiStatusFormBtnClass = 'indigo';
                $doneThisAction = false;
                $doneThisActionButton = false;
            @endphp
        <!-- Boss workflow -->
        @elseif($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_wh_manager_action') && $checkSpiLastMainSts)
            @php
                if($checkSpiLastMainSts->code == 'spi_sent_to_boss') {
                    $ppiStatusFormAction = route('spi_sent_to_wh_manager_action', [$warehouse_code, $spi->id, 'spi_sent_to_wh_manager']);
                    $ppiStatusFormBtnText = 'Sent to Warehouse Manager';
                    $ppiStatusFormBtnClass = 'indigo';
                    $doneThisAction = false;
                    $doneThisActionButton = false;
                } else {
                    $doneThisAction = true;
                    $doneThisActionButton = true;
                }
            @endphp
        <!-- Subordinate workflow -->
        @elseif($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_boss_action'))
            @php
                $ppiStatusFormAction = route('spi_sent_to_boss_action', [$warehouse_code, $spi->id, 'spi_sent_to_boss']);
                $ppiStatusFormBtnText = 'Sent to Boss';
                $ppiStatusFormBtnClass = 'indigo';
                $checkStatus = $Model('PpiSpiStatus')::checkSpiStatus($spi->id, 'spi_sent_to_boss');
                $doneThisAction = $checkStatus;
                $doneThisActionButton = $checkStatus ? true : false;
            @endphp
        @endif

        <!-- Generate Delivery Challan PDF Button (Show before challan is generated) -->
        @if($spiComplete != 'spi_all_steps_complete' && !$challanGenerated && $generalUser && auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action'))
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
