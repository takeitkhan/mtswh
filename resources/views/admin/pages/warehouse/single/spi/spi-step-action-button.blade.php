<div class="d-inline-block ms-2" id="spi_action">
    <form action="" method="POST" id="spi_action_form">
        @csrf

        <!-- ====================================================
               # Subordinate Manager Section
               # SPI Sent To Boss
               # SPI Correction(If Boss Dispute)
               # Direct SPI Sent To Warehouse Manager (If Boss Dispute)
            ========================================================= -->
        @if($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_boss_action'))
            @php
                $spiStatusFormAction =  route('spi_sent_to_boss_action', [$warehouse_code, $spi->id, 'spi_sent_to_boss']);
                $spiStatusFormBtnText = 'Sent to Boss';
                $spiStatusFormBtnClass = 'indigo';
                $spiStatusFormBtnId = '';
                $spiStatusCode = '';
                $doneThisAction = $Model('PpiSpiStatus')::checkSpiStatus($spi->id, 'spi_sent_to_boss');
                if($doneThisAction){
                    $doneThisActionButton = true;
                }
            @endphp

            <!-- ===============================
                # Boss Section
               # Boss Can Correction on Dispute Product
               # Boss can Sent to Warehouse Manager
            ==================================== -->
        @elseif($generalUser && auth()->user()->hasRoutePermission('spi_sent_to_wh_manager_action'))
            @php
                if($checkSpiLastMainSts->code == 'spi_sent_to_boss') {
                    $spiStatusFormAction =  route('spi_sent_to_wh_manager_action', [$warehouse_code, $spi->id, 'spi_sent_to_wh_manager']);
                    $spiStatusFormBtnText = 'Sent to Warehouse Manager';
                    $spiStatusFormBtnClass = 'indigo';
                    $spiStatusFormBtnId = '';
                    $spiStatusCode = '';
                    $doneThisAction =   false;
                    $doneThisActionButton = false;
                } elseif($checkSpiLastMainSts->code  == 'spi_sent_to_wh_manager'){
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                } elseif($checkSpiLastMainSts->code  == 'spi_correction_done_by_boss') {
                    $spiStatusFormAction =  route('spi_resent_to_wh_manager_action', [$warehouse_code, $spi->id, 'spi_resent_to_wh_manager']);
                    $spiStatusFormBtnText = 'Re Sent to Warehouse Manager';
                    $spiStatusFormBtnClass = 'teal';
                    $spiStatusFormBtnId = '';
                    $spiStatusCode = '';
                    $doneThisAction = false;
                    $doneThisActionButton = true;
                } elseif($checkSpiLastMainSts->code  == 'spi_dispute_by_wh_manager'){
                    $doneThisAction =  true;
                    $doneThisActionButton = true;
                } elseif($checkSpiLastMainSts->code  == 'spi_resent_to_wh_manager'){
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                } else {
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                }
            @endphp

            <!--==================================
                # Warehouse Manager  Section
                # He Can Dispute
                # He Can Check Existing product
            ====================================-->
        @elseif($generalUser && auth()->user()->hasRoutePermission('spi_dispute_by_wh_manager_action'))
            @php
                if($spi->transferable){
                    $spiStatusFormAction =  route('spi_all_steps_complete_action', [$warehouse_code, $spi->id, 'spi_all_steps_complete']);
                    $spiStatusFormBtnText = 'Close this SPI';
                    $spiStatusFormBtnClass = 'indigo';
                    $spiStatusFormBtnId = '';
                    $spiStatusCode = '';
                    $doneThisAction =   false;
                    $doneThisActionButton = true;
                }else {
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                }
            @endphp
        @endif

        @php
            $spiComplete = $Model('PpiSpiStatus')::getSpiLastMainStatus($spi->id)->code ?? null;
            if($spiComplete == 'spi_all_steps_complete'){
                $doneThisAction =  true;
                $doneThisActionButton = true;
            }
        @endphp

        @php
            $globalUser =  auth()->user()->checkUserRoleTypeGlobal();
            if($globalUser && $spi->transferable && $spiComplete != 'spi_all_steps_complete'){
                $spiStatusFormAction =  route('spi_all_steps_complete_action', [$warehouse_code, $spi->id, 'spi_all_steps_complete']);
                $spiStatusFormBtnText = 'Close this SPI';
                $spiStatusFormBtnClass = 'indigo';
                $doneThisAction =  false;
                $doneThisActionButton = false;
            }elseif($globalUser && $spiComplete != 'spi_all_steps_complete') {
                $doneThisAction = false;
                $doneThisActionButton = false;
            }
        @endphp

        <!-- View PDF Button for Closed SPI -->
        @if($spiComplete == 'spi_all_steps_complete')
            <div class="mt-3 mb-3">
                <a href="{{ route('spi_delivery_challan_preview', [$warehouse_code, $spi->id, 'spi_all_steps_complete']) }}" 
                   target="_blank" 
                   class="btn btn-md btn-success text-white py-1 w-100">
                    <i class="fa fa-file-pdf me-1"></i> View Delivery Challan
                </a>
            </div>
        @endif

        <!-- Button For Action -->
        @isset($spiStatusFormAction)
            @if(isset($doneThisAction) && $doneThisAction == false)
                <button type="button"
                        data-url="{{ $spiStatusFormAction }}"
                        data-bs-toggle="modal"
                        data-bs-target="#spiActionModal"
                        class="btn btn-md btn-{{ $spiStatusFormBtnClass }} text-white py-0"
                        id="{{ $spiStatusFormBtnId }}"
                        data-status="{{ $spiStatusCode }}"
                        data-spi-id="{{ $spi->id }}"
                        data-warehouse="{{ $warehouse_code }}">
                    {{ $spiStatusFormBtnText }}
                </button>
            
                {!! 
                    $Component::confirmModal('spiActionModal', 'form#spi_action_form', 'Are you sure?', '', '') 
                !!}
            @endif

        @endisset

    </form>
</div>

@section('bottomjs')
    @parent
    <script>
        // Flag to prevent double submission
        let spiFormSubmitting = false;
        let currentActionUrl = '';

        // Store action URL when button is clicked
        $(document).on('click', '[data-bs-target="#spiActionModal"]', function (e) {
            currentActionUrl = $(this).data('url');
            if (currentActionUrl) {
                $('#spi_action_form').prop('action', currentActionUrl);
            }
        });

        // Also set form action when modal is about to show
        $('#spiActionModal').on('show.bs.modal', function () {
            if (currentActionUrl) {
                $('#spi_action_form').prop('action', currentActionUrl);
            }
        });

        // Simple and effective double-click prevention on modal OK button
        $(document).on('click', '#spiActionModal button.modal-ok', function (e) {
            // If already submitting, prevent this click
            if (spiFormSubmitting) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Mark as submitting
            spiFormSubmitting = true;
            $(this).prop('disabled', true).addClass('disabled');
            
            // Ensure form action is set before submitting
            if (currentActionUrl) {
                $('#spi_action_form').prop('action', currentActionUrl);
            }
            
            // Let the form submit normally
        });

        // Reset flag on modal close/hide
        $('#spiActionModal').on('hide.bs.modal', function () {
            // Reset after a delay to ensure page navigation happens
            setTimeout(function() {
                spiFormSubmitting = false;
                $('#spiActionModal button.modal-ok').prop('disabled', false).removeClass('disabled');
            }, 500);
        });

        // Reset flag if form encounters an error (AJAX or validation)
        $(document).ajaxError(function() {
            spiFormSubmitting = false;
            $('#spiActionModal button.modal-ok').prop('disabled', false).removeClass('disabled');
        });
        
        // Handle form submission to prevent double submission via traditional POST
        $('#spi_action_form').on('submit', function(e) {
            // If already submitted, prevent
            if (spiFormSubmitting && spiFormSubmitting !== true) {
                e.preventDefault();
                return false;
            }
        });
        
    </script>

@endsection
