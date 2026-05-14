<div class="row" id="tbl_ppi_product">
    <div class="col-md-12 table-wrapper desktop-view mobile-view h-auto">

        @php
            // Detect if current user is Boss
            $userRoles = DB::table('role_users')->join('roles', 'roles.id', '=', 'role_users.role_id')
                ->where('role_users.user_id', auth()->user()->id)
                ->pluck('roles.name', 'roles.code')->toArray();
            $isBossUser = isset($userRoles['boss']) || in_array('Boss', $userRoles);
        @endphp

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
                            <th style="background-color: #f0f8ff; font-size: 11px; text-align: center;">PPI ID</th>
                            <th style="background-color: #fff8f0; font-size: 11px; text-align: center;">Site Code</th>
                            @if($isBossUser)
                                <th style="background-color: #ffe8e8; font-size: 11px; text-align: center;">Total QTY in PPI</th>
                                <th style="background-color: #e8f0ff; font-size: 11px; text-align: center;">Total QTY in SPI</th>
                                <th class="stock-in-hand-header" style="text-align: center;">Stock in Hand</th>
                            @endif
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

            // Adjust to available stock button
            document.querySelectorAll('.adjust-to-available').forEach(btn => {
                btn.removeEventListener('click', handleAdjustToAvailable);
                btn.addEventListener('click', handleAdjustToAvailable);
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

        // Adjust quantity to available stock handler
        function handleAdjustToAvailable(e) {
            e.preventDefault();
            const maxAvailable = this.dataset.maxAvailable;
            const productId = this.dataset.productId;
            const row = this.closest('tr');
            const qtyInput = row.querySelector('.qty-input');
            
            if (qtyInput) {
                qtyInput.value = maxAvailable;
                qtyInput.dispatchEvent(new Event('change'));
                
                // Remove red highlight
                row.classList.remove('table-danger');
                
                // Hide the adjust button and shortfall message
                const shortfallMsg = row.querySelector('.text-danger.fw-bold');
                if (shortfallMsg) shortfallMsg.remove();
                this.remove();
                
                // Show save button
                const saveBtn = row.querySelector('a.save');
                if (saveBtn) {
                    saveBtn.style.display = 'inline-block';
                }
            }
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

        // Handle product additions from alternative PPIs
        window.addEventListener('addProductFromAlternativePpi', function(event) {
            console.log('🔔 EVENT: addProductFromAlternativePpi received:', event.detail);
            
            const detail = event.detail;
            const warehouseCodeElement = document.querySelector('[data-warehouse-code]');
            const warehouseCode = warehouseCodeElement ? warehouseCodeElement.getAttribute('data-warehouse-code') : '{{ $warehouse_code }}';
            const spiId = document.getElementById('spiIdInput')?.value || '{{ $spi->id }}';
            
            if (!spiId) {
                console.error('❌ SPI ID not found');
                alert('Error: SPI ID not found');
                return;
            }

            // Send request to add product from alternative PPI
            const formData = new FormData();
            formData.append('spi_id', spiId);
            formData.append('ppi_id', detail.ppi_id);
            formData.append('product_id', detail.product_id);
            formData.append('qty', detail.quantity);
            formData.append('unit_price', detail.unit_price);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            fetch(`{{ url('/') }}/${warehouseCode}/spi/product/store`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || data.status === true) {
                    console.log('✅ Product added successfully from alternative PPI');
                    // Reload products
                    const event = new CustomEvent('addProductToSpi', {
                        detail: { ppi_id: detail.ppi_id, product_id: detail.product_id }
                    });
                    window.dispatchEvent(event);
                } else {
                    alert(data.message || 'Failed to add product');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding product from alternative PPI');
            });
        });

        // Initial attachment of event handlers when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            attachRowEventHandlers();
            
            // Handle alternative PPI modal opening
            const modal = document.getElementById('alternativePpiModal');
            if(modal) {
                modal.addEventListener('show.bs.modal', function(e) {
                    const button = e.relatedTarget;  // This is the button that triggered the modal
                    const productId = parseInt(button.dataset.productId);
                    const ppiIdWithShortfall = parseInt(button.dataset.ppiId);  // The PPI with shortfall to exclude
                    const productName = button.dataset.productName;
                    const shortfall = button.dataset.shortfall;
                    
                    // Get SPI ID from hidden input in form
                    const spiIdInput = document.querySelector('input[name="id"]');
                    const spiId = spiIdInput ? spiIdInput.value : null;
                    
                    console.log('📘 Modal opening - Product ID:', productId, 'Product Name:', productName);
                    console.log('   PPI with shortfall (EXCLUDE):', ppiIdWithShortfall);
                    console.log('   SPI ID:', spiId);
                    
                    document.getElementById('modalProductName').textContent = productName;
                    document.getElementById('modalShortfall').textContent = shortfall;
                    
                    // Only exclude the current PPI that has shortfall
                    loadAlternativePPIs(productId, ppiIdWithShortfall);
                });
            }
        });
        
        // Load alternative PPIs
        function loadAlternativePPIs(productId, excludePpiId) {
            const warehouseCodeElement = document.querySelector('[data-warehouse-code]');
            const warehouseCode = warehouseCodeElement ? warehouseCodeElement.getAttribute('data-warehouse-code') : '{{ $warehouse_code }}';
            
            document.getElementById('alternativePpiLoading').style.display = 'block';
            document.getElementById('alternativePpiContent').style.display = 'none';
            document.getElementById('alternativePpiEmpty').style.display = 'none';
            
            let url = `{{ url('/') }}/${warehouseCode}/spi/get-alternative-ppis/${productId}`;
            if(excludePpiId) {
                url += `?exclude_ppi_id=${excludePpiId}`;
            }
            console.log('📥 Fetching PPIs for product:', productId, '| Excluding PPI with shortfall:', excludePpiId, '| URL:', url);
            
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('alternativePpiLoading').style.display = 'none';
                    
                    if(data.success && data.ppis && data.ppis.length > 0) {
                        let html = '';
                        data.ppis.forEach((ppi, index) => {
                            html += `
                                <tr>
                                    <td><strong>${ppi.ppi_id}</strong></td>
                                    <td>${ppi.warehouse_name || 'N/A'}</td>
                                    <td><span class="badge bg-success">${ppi.stock_available}</span></td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm alternative-qty" 
                                            value="1" min="1" max="${ppi.stock_available}" 
                                            data-ppi-id="${ppi.ppi_id}" 
                                            data-ppi-product-id="${ppi.ppi_product_id}"
                                            data-product-id="${ppi.product_id}"
                                            data-warehouse-id="${ppi.warehouse_id}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-success add-this-ppi" 
                                            data-ppi-id="${ppi.ppi_id}"
                                            data-ppi-product-id="${ppi.ppi_product_id}"
                                            data-product-id="${ppi.product_id}"
                                            data-warehouse-id="${ppi.warehouse_id}"
                                            data-unit-price="${ppi.unit_price || 0}">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        document.getElementById('alternativePpiList').innerHTML = html;
                        document.getElementById('alternativePpiContent').style.display = 'block';
                        
                        // Attach event handlers
                        document.querySelectorAll('.add-this-ppi').forEach(btn => {
                            btn.removeEventListener('click', handleAddFromAlternativePpi);
                            btn.addEventListener('click', handleAddFromAlternativePpi);
                        });
                    } else {
                        document.getElementById('alternativePpiEmpty').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error loading alternative PPIs:', error);
                    document.getElementById('alternativePpiLoading').style.display = 'none';
                    document.getElementById('alternativePpiEmpty').style.display = 'block';
                    document.getElementById('alternativePpiEmpty').innerHTML = '<i class="fas fa-exclamation-circle"></i> Error loading PPIs. Please try again.';
                });
        }
        
        // Handle adding product from alternative PPI
        function handleAddFromAlternativePpi(e) {
            e.preventDefault();
            
            const button = this;
            const ppiId = button.dataset.ppiId;
            const productId = button.dataset.productId;
            const warehouseId = button.dataset.warehouseId;
            const unitPrice = button.dataset.unitPrice;
            const qtyInput = button.closest('tr').querySelector('.alternative-qty');
            const quantity = parseInt(qtyInput.value) || 1;
            
            if(quantity < 1) {
                alert('Quantity must be at least 1');
                return;
            }
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('alternativePpiModal'));
            if(modal) modal.hide();
            
            // Trigger event to add product from new PPI
            const event = new CustomEvent('addProductFromAlternativePpi', {
                detail: {
                    ppi_id: ppiId,
                    product_id: productId,
                    warehouse_id: warehouseId,
                    quantity: quantity,
                    unit_price: unitPrice
                }
            });
            window.dispatchEvent(event);
            
            alert(`Product will be added with ${quantity} units from PPI #${ppiId}`);
        }
    </script>

    <!-- Modal for Alternative PPIs -->
    <div class="modal fade" id="alternativePpiModal" tabindex="-1" role="dialog" aria-labelledby="alternativePpiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="alternativePpiModalLabel">
                        <i class="fas fa-box"></i> Add from Another PPI
                    </h5>
                    <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Shortfall Adjustment:</strong> এই মডালটি Shortfall পূরণ করার জন্য অন্য গুদাম থেকে পণ্য যোগ করার সুবিধা প্রদান করে।
                    </div>
                    <div id="alternativePpiLoading" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2">Finding available PPIs...</p>
                    </div>
                    <div id="alternativePpiContent" style="display: none;">
                        <div class="alert alert-info">
                            <small>Product: <strong id="modalProductName"></strong></small><br>
                            <small>Shortfall: <strong id="modalShortfall"></strong> units</small>
                        </div>
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>PPI ID</th>
                                    <th>Warehouse</th>
                                    <th>Available Stock</th>
                                    <th>Add Quantity</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="alternativePpiList">
                                <!-- PPIs will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="alternativePpiEmpty" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-info-circle"></i> No other PPIs found with this product.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table-wrapper table tbody td {
            border: 1px solid #ddd;
        }
    </style>
@endsection




