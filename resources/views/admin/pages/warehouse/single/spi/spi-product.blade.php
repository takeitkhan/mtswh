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
                            <th class="not_print">Actions</th>
                            <th class="not_print" width="80px">
                                @if(isset($disputeRoute) || isset($correctionRoute) ||  isset($physicalValidateRoute) )
                                    Correction
                                @endif
                            </th>
                            <th>Product Name</th>
                            <th>QTY</th>
                            <th>Unit</th>
                            <th class="ppi_product_price_show">Price</th>
                            <th>Product State</th>
                            <th>Health Status</th>
                            <th class="not_print">Barcode Format</th>
                            <th class="not_print">Note</th>
                            <th class="not_print">From Warehouse</th>
                            <th class="not_print">Dispute Note</th>
                            <th width="135px" class="not_print">Physical Validation</th>
                            <th class="not_print" width="80px">Save</th>
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

        /* PPI Information columns styling */
        .ppi-info-col {
            background-color: #f0f4f8 !important;
            color: #2c3e50;
        }
    </style>
    <script>
        $('form#tbl_ppi_product_form_action #ppi_product_wrap').html($('#table_ppi_product').html())
        {{--checkForSetProduct('{{ route('ppi_set_product_store', $warehouse_code) }}');--}}

        // Function to re-attach event handlers to all rows
        function attachRowEventHandlers() {
            // Track input changes
            document.querySelectorAll('.qty-input, .unit-price-input, .notes-input').forEach(input => {
                input.removeEventListener('change', handleInputChange);
                input.addEventListener('change', handleInputChange);
            });

            // Save product changes
            document.querySelectorAll('a.save').forEach(btn => {
                btn.removeEventListener('click', handleSaveProduct);
                btn.addEventListener('click', handleSaveProduct);
            });

            // Delete product - using .delete links
            document.querySelectorAll('a.delete').forEach(deleteLink => {
                deleteLink.removeEventListener('click', handleDeleteProduct);
                deleteLink.addEventListener('click', handleDeleteProduct);
            });

            // Edit product row - using .edit links
            document.querySelectorAll('a.edit').forEach(editLink => {
                editLink.removeEventListener('click', handleEditProduct);
                editLink.addEventListener('click', handleEditProduct);
            });
        }

        // Input change handler
        function handleInputChange() {
            const row = this.closest('tr');
            const saveBtn = row.querySelector('a.save');
            
            row.classList.add('product-row-modified');
            if (saveBtn) {
                saveBtn.style.display = 'inline-block';
            }
        }

        // Edit product row handler
        function handleEditProduct(e) {
            e.preventDefault();
            const row = this.closest('tr');
            const saveBtn = row.querySelector('a.save');
            
            // Highlight the row to indicate edit mode
            row.classList.add('product-row-modified');
            
            // Show save button
            if (saveBtn) {
                saveBtn.style.display = 'inline-block';
            }
            
            // Focus on first editable field
            const qtyInput = row.querySelector('.qty-input');
            if (qtyInput) {
                qtyInput.focus();
            }
            
            console.log('Edit mode activated for product:', row.dataset.productId);
        }

        // Save product handler
        function handleSaveProduct() {
            const productId = this.dataset.productId;
            const row = this.closest('tr');
            const qtyInput = row.querySelector('.qty-input');
            const priceInput = row.querySelector('.unit-price-input');
            const notesInput = row.querySelector('.notes-input');

            const qty = qtyInput.value;
            const unitPrice = priceInput.value;
            const notes = notesInput.value;

            // Check if values changed
            if (qty === qtyInput.dataset.oldValue && 
                unitPrice === priceInput.dataset.oldValue && 
                notes === notesInput.dataset.oldValue) {
                alert('No changes to save');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            const formData = new FormData();
            formData.append('spi_product_id', productId);
            formData.append('qty', qty);
            formData.append('unit_price', unitPrice);
            formData.append('notes', notes);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            const warehouseCodeElement = document.querySelector('[data-warehouse-code]');
            const warehouseCode = warehouseCodeElement ? warehouseCodeElement.getAttribute('data-warehouse-code') : '{{ $warehouse_code }}';

            fetch(`{{ url('/') }}/${warehouseCode}/spi/product/update`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.status === true) {
                    alert('Product updated successfully!');
                    qtyInput.dataset.oldValue = qty;
                    priceInput.dataset.oldValue = unitPrice;
                    notesInput.dataset.oldValue = notes;
                    row.classList.remove('product-row-modified');
                    this.style.display = 'none';
                    this.innerHTML = '<i class="fas fa-save"></i>';
                } else {
                    alert(data.message || 'Failed to update product');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating product');
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save"></i>';
            });
        }

        // Delete product handler
        function handleDeleteProduct(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this product?')) {
                return;
            }

            const productId = this.dataset.productId;
            const row = this.closest('tr');
            const warehouseCodeElement = document.querySelector('[data-warehouse-code]');
            const warehouseCode = warehouseCodeElement ? warehouseCodeElement.getAttribute('data-warehouse-code') : '{{ $warehouse_code }}';
            const url = `{{ url('/') }}/${warehouseCode}/spi/product/delete/${productId}`;

            this.disabled = true;
            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.status === true) {
                    row.remove();
                    alert('Product deleted successfully!');
                } else {
                    alert(data.message || 'Failed to delete product');
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting product');
                this.disabled = false;
                this.innerHTML = originalHtml;
            });
        }

        // Handle product additions from PPI list section
        window.addEventListener('addProductToSpi', function(event) {
            console.log('🔔 EVENT: addProductToSpi received:', event);
            console.log('📊 Event detail:', event.detail);
            
            const warehouseCodeElement = document.querySelector('[data-warehouse-code]');
            const warehouseCode = warehouseCodeElement ? warehouseCodeElement.getAttribute('data-warehouse-code') : '{{ $warehouse_code }}';
            const spiId = document.getElementById('spiIdInput')?.value || '{{ $spi->id }}';
            
            console.log('🏢 Warehouse Code:', warehouseCode);
            console.log('📋 SPI ID:', spiId);
            
            if (!spiId) {
                console.error('❌ SPI ID not found');
                return;
            }

            const fetchUrl = `{{ url('/') }}/${warehouseCode}/spi/products/${spiId}`;
            console.log('🌐 Fetching from URL:', fetchUrl);

            fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('✅ Response received, status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 API Response data:', data);
                
                if (data && data.html) {
                    console.log('✨ HTML found in response, length:', data.html.length);
                    console.log('📝 HTML preview:', data.html.substring(0, 200));
                    
                    // Parse the HTML response
                    const tempDiv = document.createElement('div');
                    tempDiv.innerHTML = data.html;
                    console.log('🔍 Parsed temp HTML, children count:', tempDiv.children.length);
                    
                    // Get the actual table tbody in the DOM
                    const actualTableBody = document.querySelector('form#tbl_ppi_product_form_action table tbody');
                    console.log('🎯 Actual table tbody found:', !!actualTableBody);
                    
                    if (actualTableBody) {
                        console.log('📊 Current tbody rows:', actualTableBody.querySelectorAll('tr').length);
                        
                        // Remove the dummy row if it exists
                        const dummyRow = actualTableBody.querySelector('tr.d-none');
                        if (dummyRow) {
                            console.log('🗑️ Removing dummy row');
                            dummyRow.remove();
                        }
                        
                        // Replace table body content with new rows
                        console.log('🔄 Replacing tbody content...');
                        actualTableBody.innerHTML = tempDiv.innerHTML;
                        console.log('✅ Tbody content replaced');
                        console.log('📊 New tbody rows:', actualTableBody.querySelectorAll('tr').length);
                        
                        // Re-attach event handlers to new rows
                        console.log('🔗 Attaching event handlers...');
                        attachRowEventHandlers();
                        console.log('✅ Event handlers attached successfully');
                    } else {
                        console.error('❌ Actual table body NOT found in DOM');
                        console.log('🔍 Available tables:', document.querySelectorAll('form#tbl_ppi_product_form_action table').length);
                    }
                } else {
                    console.error('❌ No HTML in response:', data);
                }
            })
            .catch(error => {
                console.error('❌ AJAX Error:', error);
                console.error('📍 Error details:', error.message);
            });
        });

        // Initial attachment of event handlers when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            attachRowEventHandlers();
        });
    </script>


    <style>
        .table-wrapper table tbody td {
            border: 1px solid #ddd;
        }
    </style>
@endsection




