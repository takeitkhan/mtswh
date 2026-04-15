<!-- Product Table Container -->
<div id="spiProductCardsContainer" class="mt-4 card border-0 shadow-sm" data-warehouse-code="{{ request()->get('warehouse_code') }}">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="spiProductCards">
            <thead class="table-light">
                <tr>
                    <th style="width: 30%;">Product Name</th>
                    <th style="width: 15%;">Stock</th>
                    <th style="width: 15%;">Unit Price</th>
                    <th style="width: 12%;">Qty</th>
                    <th style="width: 15%;">Notes</th>
                    <th style="width: 13%;" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Products will be added here dynamically -->
                @if(!empty($spi->id) && $spi->spiProducts()->count() > 0)
                    @foreach($spi->spiProducts as $product)
                        <tr data-product-id="{{ $product->id }}" data-product-row-id="{{ $product->id }}">
                            <td>
                                <div>
                                    <strong>{{ $product->product->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $product->product->product_type ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td>
                                <strong class="text-primary">{{ $product->product->current_stock ?? 0 }} {{ $product->product->unit ?? 'pcs' }}</strong>
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm unit-price-input" value="{{ $product->unit_price ?? 0 }}" step="0.01" min="0" data-product-id="{{ $product->id }}" data-old-value="{{ $product->unit_price ?? 0 }}">
                            </td>
                            <td>
                                <input type="number" class="form-control form-control-sm qty-input" value="{{ $product->qty }}" min="1" data-product-id="{{ $product->id }}" data-old-value="{{ $product->qty }}">
                            </td>
                            <td>
                                <input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="{{ $product->notes ?? '' }}" data-product-id="{{ $product->id }}" data-old-value="{{ $product->notes ?? '' }}">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-success save-product" data-product-id="{{ $product->id }}" title="Save Changes">
                                    <i class="fas fa-save"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-product" data-product-id="{{ $product->id }}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6">
                            <div class="alert alert-info text-center mb-0">
                                <i class="fas fa-info-circle"></i> No products added yet. Add a product to get started.
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<style>
#spiProductCards tbody tr {
    transition: background-color 0.2s ease;
}

#spiProductCards tbody tr:hover {
    background-color: #f8f9fa;
}

.qty-input, .unit-price-input, .notes-input {
    border: 1px solid #dee2e6;
}

.qty-input:focus, .unit-price-input:focus, .notes-input:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.save-product {
    margin-right: 4px;
}

.product-modified {
    background-color: #fff3cd !important;
}

.product-saving {
    opacity: 0.6;
    pointer-events: none;
}
</style>

<script>
// Handle immediate row addition from PPI bulk add
window.addEventListener('addProductToSpi', function(e) {
    const detail = e.detail;
    const tbody = document.querySelector('#spiProductCards tbody');
    
    // Remove empty message if exists
    const emptyRow = tbody.querySelector('tr td[colspan="6"]');
    if (emptyRow) {
        emptyRow.closest('tr').remove();
    }

    // Create new product row
    const row = document.createElement('tr');
    row.setAttribute('data-product-id', detail.productId);
    row.innerHTML = `
        <td>
            <div>
                <strong>${detail.productName}</strong>
            </div>
        </td>
        <td>
            <span class="text-muted">Updating...</span>
        </td>
        <td>
            <input type="number" class="form-control form-control-sm unit-price-input" value="${detail.unitPrice || 0}" step="0.01" min="0" data-product-id="${detail.productId}" data-old-value="${detail.unitPrice || 0}">
        </td>
        <td>
            <input type="number" class="form-control form-control-sm qty-input" value="${detail.qty || 1}" min="1" data-product-id="${detail.productId}" data-old-value="${detail.qty || 1}">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="" data-product-id="${detail.productId}" data-old-value="">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-success save-product" data-product-id="${detail.productId}" title="Save Changes">
                <i class="fas fa-save"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger delete-product" data-product-id="${detail.productId}" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    attachRowEvents(row);
});

// Attach events to a row
function attachRowEvents(row) {
    const productId = row.getAttribute('data-product-id');
    
    // Track input changes
    const inputs = row.querySelectorAll('.qty-input, .unit-price-input, .notes-input');
    inputs.forEach(input => {
        input.addEventListener('change', function() {
            row.classList.add('product-modified');
        });
    });

    // Save button
    const saveBtn = row.querySelector('.save-product');
    saveBtn.addEventListener('click', function() {
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
            showAlert('No changes to save', 'info');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        row.classList.add('product-saving');

        const formData = new FormData();
        formData.append('spi_product_id', productId);
        formData.append('qty', qty);
        formData.append('unit_price', unitPrice);
        formData.append('notes', notes);
        formData.append('_token', document.querySelector('input[name="_token"]').value);

        const warehouseCode = document.getElementById('spiProductCardsContainer').getAttribute('data-warehouse-code');

        fetch(`{{ url('/') }}/${warehouseCode}/spi/product/update`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === true) {
                showAlert('Product updated successfully!', 'success');
                qtyInput.dataset.oldValue = qty;
                priceInput.dataset.oldValue = unitPrice;
                notesInput.dataset.oldValue = notes;
                row.classList.remove('product-modified');
            } else {
                showAlert(data.message || 'Failed to update product', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error updating product', 'danger');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i>';
            row.classList.remove('product-saving');
        });
    });

    // Delete button
    const deleteBtn = row.querySelector('.delete-product');
    deleteBtn.addEventListener('click', function() {
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        row.classList.add('product-saving');

        const spiIdInput = document.getElementById('spiIdInput');
        const spiId = spiIdInput ? spiIdInput.value : null;
        const warehouseCode = document.getElementById('spiProductCardsContainer').getAttribute('data-warehouse-code');
        
        if (!spiId || !warehouseCode) {
            showAlert('Error: Missing required data', 'danger');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            row.classList.remove('product-saving');
            return;
        }

        const deleteUrl = `{{ url('/') }}/${warehouseCode}/spi/product/delete/${productId}`;

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                spi_id: spiId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || data.status === true) {
                showAlert('Product removed successfully!', 'success');
                row.remove();
                checkAndShowEmptyMessage();
            } else {
                showAlert(data.message || 'Failed to remove product', 'danger');
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                row.classList.remove('product-saving');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error removing product', 'danger');
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
            row.classList.remove('product-saving');
        });
    });
}

function checkAndShowEmptyMessage() {
    const tbody = document.querySelector('#spiProductCards tbody');
    const rows = tbody.querySelectorAll('tr');
    
    if (rows.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6">
                    <div class="alert alert-info text-center mb-0">
                        <i class="fas fa-info-circle"></i> No products added yet. Add a product to get started.
                    </div>
                </td>
            </tr>
        `;
    }
}

// Initialize events on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#spiProductCards tbody tr').forEach(row => {
        if (!row.querySelector('td[colspan]')) {
            attachRowEvents(row);
        }
    });
});

// Refresh table when products updated from server
window.addEventListener('ppi-products-updated', function() {
    console.log('ppi-products-updated event received');
    const spiIdInput = document.getElementById('spiIdInput');
    const spiId = spiIdInput ? spiIdInput.value : document.querySelector('[data-spi-id]')?.dataset.spiId;
    
    let warehouseCode = document.getElementById('spiProductCardsContainer')?.getAttribute('data-warehouse-code');
    if (!warehouseCode) {
        const form = document.querySelector('form[action*="/spi/update"]');
        if (form) {
            const action = form.getAttribute('action');
            warehouseCode = action?.split('/')[1];
        }
    }
    
    if (!spiId || !warehouseCode) {
        console.warn('Missing SPI ID or warehouse code for product refresh');
        return;
    }

    const tbody = document.querySelector('#spiProductCards tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center p-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;

    fetch(`{{ url('/') }}/${warehouseCode}/spi/products/${spiId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Product refresh response:', data);
        if (data.success && data.products) {
            tbody.innerHTML = '';

            if (data.products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6">
                            <div class="alert alert-info text-center mb-0">
                                <i class="fas fa-info-circle"></i> No products added yet.
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            data.products.forEach(product => {
                const row = document.createElement('tr');
                row.setAttribute('data-product-id', product.id);
                row.innerHTML = `
                    <td>
                        <div>
                            <strong>${product.product_name}</strong>
                            <br>
                            <small class="text-muted">${product.product_type || 'N/A'}</small>
                        </div>
                    </td>
                    <td>
                        <strong class="text-primary">${product.current_stock || 0} ${product.unit || 'pcs'}</strong>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm unit-price-input" value="${product.unit_price || 0}" step="0.01" min="0" data-product-id="${product.id}" data-old-value="${product.unit_price || 0}">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm qty-input" value="${product.qty}" min="1" data-product-id="${product.id}" data-old-value="${product.qty}">
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm notes-input" placeholder="Notes" value="${product.notes || ''}" data-product-id="${product.id}" data-old-value="${product.notes || ''}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-success save-product" data-product-id="${product.id}" title="Save Changes">
                            <i class="fas fa-save"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger delete-product" data-product-id="${product.id}" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
                attachRowEvents(row);
            });
        }
    })
    .catch(error => {
        console.error('Error updating products:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-danger text-center p-4">
                    <i class="fas fa-exclamation-triangle"></i> Error loading products
                </td>
            </tr>
        `;
    });
});
</script>
