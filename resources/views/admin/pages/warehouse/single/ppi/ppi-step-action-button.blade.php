<div class="d-inline-block ms-2" id="ppi_action">
    <form action="" method="POST" id="ppi_action_form">
        @csrf
        @php
            $ppiComplete = $Model('PpiSpiStatus')::getPpiLastStatus($ppi->id)->code;
        @endphp
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
                $ppiStatusFormBtnId = '';
                $ppiStatusCode = '';
                $warehouse_code = '';
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
                    $ppiStatusFormBtnId = '';
                    $ppiStatusCode = '';
                    $warehouse_code = '';
                    $doneThisAction =   false;
                    $doneThisActionButton = false;
                } elseif($checkPpiLastSts->code  == 'ppi_sent_to_wh_manager'){
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                } elseif($checkPpiLastSts->code  == 'ppi_correction_done_by_boss') {
                    $ppiStatusFormAction =  route('ppi_resent_to_wh_manager_action', [$warehouse_code, $ppi->id, 'ppi_resent_to_wh_manager']);
                    $ppiStatusFormBtnText = 'Re Sent to Warehouse Manager';
                    $ppiStatusFormBtnClass = 'teal';
                    $ppiStatusFormBtnId = '';
                    $ppiStatusCode = '';
                    $warehouse_code = '';
                    $doneThisAction = false;
                    $doneThisActionButton = true;
                } elseif($checkPpiLastSts->code  == 'ppi_dispute_by_wh_manager'){
                    $doneThisAction =  true;
                    $doneThisActionButton = true;
                } elseif($checkPpiLastSts->code  == 'ppi_resent_to_wh_manager'){
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
                # He can Print Barcode
                # He cam stock in Product
            ====================================-->
        @elseif($generalUser && auth()->user()->hasRoutePermission('ppi_dispute_by_wh_manager_action'))
            @php
                if($singleProductValidationDone && $setProductValidationDone && $ppiComplete != 'ppi_all_steps_complete'){
                    $ppiStatusFormAction =  route('ppi_all_steps_complete_action', [$warehouse_code, $ppi->id, 'ppi_all_steps_complete']);
                    $ppiStatusFormBtnText = 'Print Challan';
                    $ppiStatusFormBtnClass = 'success';
                    $ppiStatusFormBtnId = '';
                    $ppiStatusCode = '';
                    $doneThisAction =   false;
                    $doneThisActionButton = false;
                }else {
                    $doneThisAction =   true;
                    $doneThisActionButton = true;
                }
            @endphp
        @endif
    
    


        @php
            $globalUser =  auth()->user()->checkUserRoleTypeGlobal();
            if($globalUser && $singleProductValidationDone && $setProductValidationDone && $ppiComplete != 'ppi_all_steps_complete'){
                $ppiStatusFormAction =  route('ppi_all_steps_complete_action', [$warehouse_code, $ppi->id, 'ppi_all_steps_complete']);
                $ppiStatusFormBtnText = 'Close this PPI';
                $ppiStatusFormBtnClass = 'indigo';
                $doneThisAction =  false;
                $doneThisActionButton = false;
            }elseif($globalUser && $ppiComplete != 'ppi_all_steps_complete') {
                $doneThisAction = false;
                $doneThisActionButton = false;
            }
        @endphp
    
        <!-- View PDF Button for Closed PPI -->
        @if($ppiComplete == 'ppi_all_steps_complete')
            <div class="mt-3 mb-3">
                <a href="{{ route('ppi_challan_pdf_preview', [$warehouse_code, $ppi->id, 'ppi_all_steps_complete']) }}" 
                   target="_blank" 
                   class="btn btn-md btn-success text-white py-1 w-100">
                    <i class="fa fa-file-pdf me-1"></i> View Challan PDF
                </a>
            </div>
        @endif
    
        <!-- Button For Action -->
        @isset($ppiStatusFormAction)
            @if(isset($doneThisAction) && $doneThisAction == false)
                <button type="button"
                        data-url="{{ $ppiStatusFormAction }}"
                        {{-- Only add modal if NOT PDF button --}}
                        @if($ppiStatusFormBtnId !== 'printChallanBtn')
                            data-bs-toggle="modal"
                            data-bs-target="#ppiActionModal"
                        @endif
                        class="btn btn-md btn-{{ $ppiStatusFormBtnClass }} text-white py-0"
                        id="{{ $ppiStatusFormBtnId }}"
                        data-status="{{ $ppiStatusCode }}"
                        data-ppi-id="{{ $ppi->id }}"
                        data-warehouse="{{ $warehouse_code }}">
                    {{ $ppiStatusFormBtnText }}
                </button>
            
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
        // Flag to prevent double submission
        let ppiFormSubmitting = false;
        let currentActionUrl = '';

        // Store action URL when button is clicked
        $(document).on('click', '[data-bs-target="#ppiActionModal"]', function (e) {
            currentActionUrl = $(this).data('url');
            if (currentActionUrl) {
                $('#ppi_action_form').prop('action', currentActionUrl);
            }
        });

        // Also set form action when modal is about to show
        $('#ppiActionModal').on('show.bs.modal', function () {
            if (currentActionUrl) {
                $('#ppi_action_form').prop('action', currentActionUrl);
            }
        });

        // Simple and effective double-click prevention on modal OK button
        $(document).on('click', '#ppiActionModal button.modal-ok', function (e) {
            // If already submitting, prevent this click
            if (ppiFormSubmitting) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                return false;
            }
            
            // Mark as submitting
            ppiFormSubmitting = true;
            $(this).prop('disabled', true).addClass('disabled');
            
            // Ensure form action is set before submitting
            if (currentActionUrl) {
                $('#ppi_action_form').prop('action', currentActionUrl);
            }
            
            // Let the form submit normally - don't preventDefault
            // The modal component will handle the form submission
        });

        // Reset flag on modal close/hide
        $('#ppiActionModal').on('hide.bs.modal', function () {
            // Reset after a delay to ensure page navigation happens
            setTimeout(function() {
                ppiFormSubmitting = false;
                $('#ppiActionModal button.modal-ok').prop('disabled', false).removeClass('disabled');
            }, 500);
        });

        // Reset flag if form encounters an error (AJAX or validation)
        $(document).ajaxError(function() {
            ppiFormSubmitting = false;
            $('#ppiActionModal button.modal-ok').prop('disabled', false).removeClass('disabled');
        });
        
        // Handle form submission to prevent double submission via traditional POST
        $('#ppi_action_form').on('submit', function(e) {
            // If already submitted, prevent
            if (ppiFormSubmitting && ppiFormSubmitting !== true) {
                e.preventDefault();
                return false;
            }
        });
        
    </script>

    
@endsection