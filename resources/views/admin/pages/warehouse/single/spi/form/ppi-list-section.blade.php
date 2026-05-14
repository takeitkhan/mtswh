<!-- PPI List Section (shown after product selection) -->
<div id="spiPpiListSection" data-ppi-list-section data-spi-id="{{ $spi->id ?? '' }}" class="card mt-4 mb-4 border-info" style="display: none;">
    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-list"></i> Select from Available PPIs - <span id="selectedProductName"></span>
        </h6>
        <small>Select multiple PPIs and click "Add Selected" to add them all at once</small>
    </div>
    
    <!-- DEBUG: Show user role info -->
    <div class="alert alert-warning m-2" style="font-size: 11px;">
        <strong>DEBUG INFO:</strong> 
        User ID: {{ auth()->user()->id }} | 
        User Name: {{ auth()->user()->name }} | 
        @php
            $userRoles = DB::table('role_users')
                ->join('roles', 'roles.id', '=', 'role_users.role_id')
                ->where('role_users.user_id', auth()->user()->id)
                ->pluck('roles.name', 'roles.code')
                ->toArray();
            
            $isSubordinateManagerBackend = isset($userRoles['subordinate_manager']) || in_array('Subordinate Manager', $userRoles);
        @endphp
        Roles: @if($userRoles) {{ implode(', ', $userRoles) }} @else <span style="color: #dc3545;">No Roles Found</span> @endif
        <br/>
        <span id="userRoleDebug" style="font-weight: bold; color: {{ $isSubordinateManagerBackend ? '#dc3545' : '#28a745' }};">
            Status: {{ $isSubordinateManagerBackend ? '✓ SUBORDINATE MANAGER DETECTED' : '✗ NOT Subordinate Manager' }}
        </span>
    </div>
    
    <div class="card-body">
        <!-- PPI List Table -->
        <div class="table-responsive">
            <table class="table table-hover table-sm" id="ppiListTable" style="font-size: 12px;">
                <thead class="table-light" id="ppiTableHead">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" id="selectAllPpis" title="Select/Deselect all PPIs"></th>
                        <th>PPI ID</th>
                        <th>Warehouse/Supplier</th>
                        <th class="stock-in-hand-col">Stock in Hand</th>
                        <th>Product State</th>
                        <th>Health Status</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Unit Price (SPI)</th>
                        <th>Total Price</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="ppiListBody">
                    <!-- PPIs will be loaded here -->
                </tbody>
            </table>
        </div>

        <!-- Loading Spinner -->
        <div id="ppiListLoading" class="text-center" style="display: none;">
            <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- No Results -->
        <div id="ppiListEmpty" class="alert alert-warning text-center mb-0" style="display: none;">
            <i class="fas fa-inbox"></i> No PPIs found for this product
        </div>
    </div>

    <!-- Footer with Action Buttons -->
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div>
            <button type="button" id="addSelectedPpisBtn" data-action="add-selected-ppis" class="btn btn-success" style="display: none;" onclick="addMultipleSelectedPpis()">
                <i class="fas fa-plus-circle"></i> Add Selected (<span id="selectedPpiCount">0</span>)
            </button>
        </div>
        <button type="button" id="backToProductSelect" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Product Selection
        </button>
    </div>
</div>

<script>
// Ensure showAlert is available globally
if (typeof showAlert === 'undefined') {
    window.showAlert = function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add to top of page
        const container = document.querySelector('.card.border-teal') || document.querySelector('.card.border-info') || document.body;
        if (container && container.parentElement) {
            container.parentElement.insertBefore(alertDiv, container);
        } else {
            document.body.insertBefore(alertDiv, document.body.firstChild);
        }
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    };
}

document.addEventListener('DOMContentLoaded', function() {
    const ppiListSection = document.getElementById('spiPpiListSection');
    const backBtn = document.getElementById('backToProductSelect');
    let isSubordinateManager = false; // Track if current user is subordinate manager

    // Handle "Select All" checkbox
    document.addEventListener('change', function(e) {
        if (e.target.id === 'selectAllPpis') {
            const checkboxes = document.querySelectorAll('.ppi-select-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
            updateSelectedCount();
        }
    });

    // Handle individual checkbox changes and count updates
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('ppi-select-checkbox')) {
            updateSelectedCount();
        }
    });

    // Listen for product selection from product-add-section (old method)
    window.addEventListener('productSelected', function(e) {
        const productId = e.detail.productId;
        const productName = e.detail.productName;
        
        // Update header to show selected product
        const selectedProductNameEl = document.getElementById('selectedProductName');
        if (selectedProductNameEl) {
            selectedProductNameEl.textContent = productName;
        }
        
        // Load and display PPI list for this product
        loadPpiListForProduct(productId);
        
        // Show PPI list section
        if (ppiListSection) {
            ppiListSection.style.display = 'block';
        }
    });

    // Listen for renderPpiList event from Select2 in product-add-section (new method)
    window.addEventListener('renderPpiList', function(e) {
        const ppis = e.detail.ppis;
        renderPpiList(ppis, isSubordinateManager);
    });

    // Back to product selection
    if (backBtn) {
        backBtn.addEventListener('click', function() {
            if (ppiListSection) {
                ppiListSection.style.display = 'none';
            }
            // Reset product select in product-add-section
            const productSelect = document.getElementById('spiProductSelect');
            if (productSelect) {
                productSelect.value = '';
                productSelect.focus();
            }
        });
    }

    // Load PPI list for selected product
    function loadPpiListForProduct(productId) {
        const spiIdInput = document.getElementById('spiIdInput');
        const spiId = spiIdInput ? spiIdInput.value : null;

        if (!spiId) {
            showAlert('SPI ID not found', 'danger');
            return;
        }

        const ppiListBody = document.getElementById('ppiListBody');
        const ppiListLoading = document.getElementById('ppiListLoading');
        const ppiListEmpty = document.getElementById('ppiListEmpty');

        // Show loading state
        ppiListLoading.style.display = 'block';
        ppiListEmpty.style.display = 'none';
        ppiListBody.innerHTML = '';

        // Fetch PPIs for this product
        fetch(`{{ route('spi_get_ppi_list_for_product', $warehouse_code) }}?product_id=${productId}&spi_id=${spiId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(response => response.json())
        .then(data => {
            ppiListLoading.style.display = 'none';

            if (data.success && data.ppis && data.ppis.length > 0) {
                // DEBUG: Log all info
                console.log('=== PPI List API Response ===');
                console.log('User ID:', data.debug.user_id);
                console.log('User Name:', data.debug.user_name);
                console.log('Is Subordinate Manager:', data.debug.is_subordinate_manager);
                console.log('All User Roles:', data.debug.all_user_roles);
                
                // Store the flag for use in renderPpiList
                isSubordinateManager = data.is_subordinate_manager;
                
                if (data.is_subordinate_manager) {
                    console.log('✓ Stock column will NOT be rendered for Subordinate Manager');
                } else {
                    console.log('✗ Stock column WILL be rendered - User is NOT Subordinate Manager');
                }
                
                renderPpiList(data.ppis, data.is_subordinate_manager);
                ppiListEmpty.style.display = 'none';
            } else {
                ppiListEmpty.style.display = 'block';
                ppiListBody.innerHTML = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            ppiListLoading.style.display = 'none';
            showAlert('Error loading PPI list: ' + error.message, 'danger');
            ppiListEmpty.style.display = 'block';
        });
    }

    // Render PPI list in table
    function renderPpiList(ppis, isSubordinateManager = false) {
        const ppiListBody = document.getElementById('ppiListBody');
        ppiListBody.innerHTML = '';

        // Update header - hide/show Stock column based on role
        const stockHeaderCol = document.querySelector('th.stock-in-hand-col');
        if(stockHeaderCol) {
            stockHeaderCol.style.display = isSubordinateManager ? 'none' : 'table-cell';
        }

        ppis.forEach((ppi, index) => {
            const row = document.createElement('tr');
            row.setAttribute('data-ppi-id', ppi.ppi_id);
            row.setAttribute('data-product-id', ppi.product_id);
            
            // Stock column - conditionally included
            const stockColumnHTML = isSubordinateManager ? '' : `<td class="stock-in-hand-col">${ppi.stock_in_hand !== null ? ppi.stock_in_hand : 'N/A'}</td>`;
            
            row.innerHTML = `
                <td style="width: 40px;"><input type="checkbox" class="ppi-select-checkbox" data-ppi-id="${ppi.ppi_id}" data-product-id="${ppi.product_id}" data-index="${index}"></td>
                <td><strong>${ppi.ppi_id}</strong></td>
                <td><span class="badge bg-success">${ppi.warehouse || 'N/A'}</span><br>${ppi.supplier || 'N/A'}</td>
                ${stockColumnHTML}
                <td>${ppi.product_state || 'New'}</td>
                <td>${ppi.health_status || 'Useable'}</td>
                <td>৳${parseFloat(ppi.unit_price || 0).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm ppi-qty" value="1" min="1" data-index="${index}">
                </td>
                <td>
                    <input type="number" class="form-control form-control-sm ppi-unit-price" value="${ppi.unit_price || 0}" min="0" data-index="${index}" step="0.01">
                </td>
                <td class="ppi-total-price-cell">৳<span class="ppi-total-price">${parseFloat(ppi.unit_price || 0).toFixed(2)}</span></td>
                <td>
                    <input type="text" class="form-control form-control-sm ppi-notes" placeholder="Notes" data-index="${index}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-success add-from-ppi" data-ppi-id="${ppi.ppi_id}" data-product-id="${ppi.product_id}" data-index="${index}">
                        <i class="fas fa-plus"></i>
                    </button>
                </td>
            `;
            ppiListBody.appendChild(row);

            // Add event listeners for this row
            const checkbox = row.querySelector('.ppi-select-checkbox');
            const qtyInput = row.querySelector('.ppi-qty');
            const unitPriceInput = row.querySelector('.ppi-unit-price');
            const addBtn = row.querySelector('.add-from-ppi');

            // Update total price when qty or unit price changes
            [qtyInput, unitPriceInput].forEach(input => {
                input.addEventListener('change', function() {
                    updateTotalPrice(row);
                });
            });

            // Add button click
            addBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addProductFromPpi(ppi, qtyInput.value, unitPriceInput.value, row.querySelector('.ppi-notes').value);
            });
        });
    }

    // Update total price calculation
    function updateTotalPrice(row) {
        const qtyInput = row.querySelector('.ppi-qty');
        const unitPriceInput = row.querySelector('.ppi-unit-price');
        const totalCell = row.querySelector('.ppi-total-price');

        const qty = parseFloat(qtyInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const total = (qty * unitPrice).toFixed(2);

        totalCell.textContent = total;
    }

    // Add product from PPI
    function addProductFromPpi(ppi, qty, unitPrice, notes) {
        if (!qty || qty < 1) {
            showAlert('Please enter valid quantity', 'warning');
            return;
        }

        const spiIdInput = document.getElementById('spiIdInput');
        const spiId = spiIdInput ? spiIdInput.value : null;

        if (!spiId) {
            showAlert('SPI ID not found', 'danger');
            return;
        }

        // Get the add button for this row
        const addBtn = document.querySelector(`.add-from-ppi[data-ppi-id="${ppi.ppi_id}"]`);
        if (!addBtn) return;

        const originalText = addBtn.innerHTML;
        addBtn.disabled = true;
        addBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Send AJAX to add product using FormData for proper array parsing
        const formData = new FormData();
        formData.append('spi_id', spiId);
        formData.append('product[' + ppi.product_id + '][product_id]', ppi.product_id);
        formData.append('product[' + ppi.product_id + '][qty]', qty);
        formData.append('product[' + ppi.product_id + '][unit_price]', unitPrice);
        formData.append('product[' + ppi.product_id + '][notes]', notes);
        formData.append('product[' + ppi.product_id + '][ppi_id]', ppi.ppi_id);
        formData.append('_token', document.querySelector('input[name="_token"]').value);

        fetch('{{ route("spi_product_store", $warehouse_code) }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.html) {
                showAlert('Product added successfully!', 'success');
                
                // Update the products table directly with the new HTML from the response
                const tableBody = document.querySelector('form#tbl_ppi_product_form_action table tbody');
                if (tableBody) {
                    // Remove the dummy row if it exists
                    const dummyRow = tableBody.querySelector('tr.d-none');
                    if (dummyRow) {
                        dummyRow.remove();
                    }
                    
                    // Replace table body content with the updated product rows
                    tableBody.innerHTML = data.html;
                    console.log('✅ Table updated with new product');
                    
                    // Re-attach event handlers to the new rows
                    if (typeof attachRowEventHandlers === 'function') {
                        attachRowEventHandlers();
                        console.log('✅ Event handlers re-attached');
                    }
                }

                // Reset
                const productSelectEl = document.getElementById('spiProductSelect');
                if (productSelectEl) {
                    productSelectEl.value = '';
                    if (window.jQuery) jQuery(productSelectEl).trigger('change');
                }
                const ppiListSectionEl = document.getElementById('spiPpiListSection');
                if (ppiListSectionEl) ppiListSectionEl.style.display = 'none';
            } else {
                showAlert(data.message || 'Failed to add product', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error adding product: ' + error.message, 'danger');
        })
        .finally(() => {
            addBtn.disabled = false;
            addBtn.innerHTML = originalText;
        });
    }
});
</script>

<!-- Add multiple selected PPIs function (Global scope for onclick handler) -->
<script>
// Update selected count and show/hide "Add Selected" button
function updateSelectedCount() {
    const selectedCount = document.querySelectorAll('.ppi-select-checkbox:checked').length;
    const countElement = document.getElementById('selectedPpiCount');
    if (countElement) {
        countElement.textContent = selectedCount;
    }
    const addSelectedBtn = document.getElementById('addSelectedPpisBtn');
    if (addSelectedBtn) {
        addSelectedBtn.style.display = selectedCount > 0 ? 'block' : 'none';
    }
}

function addMultipleSelectedPpis() {
    const checkedRows = document.querySelectorAll('.ppi-select-checkbox:checked');
    if (checkedRows.length === 0) {
        showAlert('Please select at least one PPI to add', 'warning');
        return;
    }

    const addMultiBtn = document.querySelector('[data-action="add-selected-ppis"]');
    const originalText = addMultiBtn.innerHTML;
    addMultiBtn.disabled = true;
    addMultiBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';

    const formData = new FormData();
    formData.append('spi_id', document.querySelector('[data-spi-id]')?.dataset.spiId || '');
    formData.append('_token', document.querySelector('input[name="_token"]').value);

    let productIndex = 0;
    const productIds = new Set();

    // Collect data from all checked rows
    checkedRows.forEach((checkbox) => {
        const row = checkbox.closest('tr');
        if (!row) return;

        // Get ppiId and productId from multiple sources for robustness
        const ppiId = checkbox.dataset.ppiId || row.dataset.ppiId;
        const productId = checkbox.dataset.productId || row.dataset.productId;
        
        const qtyInput = row.querySelector('input.ppi-qty');
        const priceInput = row.querySelector('input.ppi-unit-price');
        const notesInput = row.querySelector('input.ppi-notes');

        const qty = qtyInput?.value || '';
        const unitPrice = priceInput?.value || '';
        const notes = notesInput?.value || '';

        if (!productId || !ppiId) {
            console.warn('Missing productId or ppiId for row', row);
            return;
        }

        // Build FormData with product[index][field] structure
        formData.append(`product[${productIndex}][product_id]`, productId);
        formData.append(`product[${productIndex}][ppi_id]`, ppiId);
        if (qty) formData.append(`product[${productIndex}][qty]`, qty);
        if (unitPrice) formData.append(`product[${productIndex}][unit_price]`, unitPrice);
        if (notes) formData.append(`product[${productIndex}][notes]`, notes);

        productIds.add(productId);
        productIndex++;
    });

    if (productIndex === 0) {
        showAlert('No valid PPIs selected to add', 'warning');
        addMultiBtn.disabled = false;
        addMultiBtn.innerHTML = originalText;
        return;
    }

    // Send AJAX request
    fetch('{{ route("spi_product_store", $warehouse_code) }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.html) {
            console.log('✅ Products added successfully, updating table...');
            showAlert(`Successfully added ${productIndex} product(s)!`, 'success');

            // Update the products table directly with the new HTML from the response
            const tableBody = document.querySelector('form#tbl_ppi_product_form_action table tbody');
            if (tableBody) {
                // Remove the dummy row if it exists
                const dummyRow = tableBody.querySelector('tr.d-none');
                if (dummyRow) {
                    dummyRow.remove();
                }
                
                // Replace table body content with the updated product rows
                tableBody.innerHTML = data.html;
                console.log('✅ Table updated with new products');
                
                // Re-attach event handlers to the new rows
                if (typeof attachRowEventHandlers === 'function') {
                    attachRowEventHandlers();
                    console.log('✅ Event handlers re-attached');
                }
            }

            // Clear all checkboxes
            document.querySelectorAll('.ppi-select-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAllPpis').checked = false;

            // Hide the Add Selected button and PPI list
            updateSelectedCount();
            const ppiListSection = document.querySelector('[data-ppi-list-section]');
            if (ppiListSection) {
                setTimeout(() => {
                    ppiListSection.style.display = 'none';
                }, 500);
            }

            // Reset product select
            const productSelect = document.querySelector('select[name="product_id"]');
            if (productSelect) productSelect.value = '';
        } else {
            showAlert(data.message || 'Failed to add products', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error adding products: ' + error.message, 'danger');
    })
    .finally(() => {
        addMultiBtn.disabled = false;
        addMultiBtn.innerHTML = originalText;
    });
}
</script>

<style>
/* Stock in Hand column styling */
#ppiListTable th.stock-in-hand-col,
#ppiListTable td.stock-in-hand-col {
    display: table-cell;
}

/* When hidden */
#ppiListTable th.stock-in-hand-col[style*="display: none"],
#ppiListTable td.stock-in-hand-col[style*="display: none"] {
    display: none !important;
}

/* Ensure consistent column alignment */
#ppiListTable tr td:nth-child(4),
#ppiListTable tr th:nth-child(4) {
    /* Stock in Hand column */
}
</style>
