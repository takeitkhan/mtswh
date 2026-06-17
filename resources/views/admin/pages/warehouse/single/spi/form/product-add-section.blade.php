<!-- Product Selection Section -->
<div class="card mt-4 mb-4 border-primary">
    <!-- Hidden input for spi_id -->
    <input type="hidden" id="spiIdInput" name="spi_id" value="{{ $spi->id ?? '' }}">
    
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0">
            <i class="fas fa-search"></i> Search & Select Product for PPI
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <!-- Product Selection -->
            <div class="col-12">
                <label for="spiProductSelect" class="form-label">
                    <strong>Select Product to view available PPIs</strong>
                    <span class="text-danger">*</span>
                </label>
                <select id="spiProductSelect" class="form-select select-box-modal" required>
                    <option value="">-- Choose a Product --</option>
                    @php
                        $getProducts = $Model('Product')::thisWarehouseProducts(['product_type', $spi->ppi_spi_type]);
                    @endphp
                    @foreach ($getProducts as $product)
                        <option value="{{ $product->id }}" data-name="{{ $product->name }}">
                            {{ $product->name }} ({{ $product->product_type }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2 for product select
    $('#spiProductSelect').select2({
        placeholder: "Select a Product",
        width: '100%',
        allowClear: true
    });

    const productSelect = document.getElementById('spiProductSelect');
    const spiId = document.getElementById('spiIdInput')?.value;

    // When product is selected via Select2
    $('#spiProductSelect').on('change', function() {
        const productId = this.value;
        
        if (productId) {
            const productName = this.options[this.selectedIndex].text;
            
            // Show loading state
            const ppiListSection = document.querySelector('[data-ppi-list-section]');
            const ppiListLoading = document.getElementById('ppiListLoading');
            const ppiListBody = document.getElementById('ppiListBody');
            const ppiListEmpty = document.getElementById('ppiListEmpty');

            if (ppiListSection) {
                ppiListSection.style.display = 'block';
            }
            if (ppiListLoading) {
                ppiListLoading.style.display = 'block';
            }
            if (ppiListEmpty) {
                ppiListEmpty.style.display = 'none';
            }
            if (ppiListBody) {
                ppiListBody.innerHTML = '';
            }

            // Update the product name in PPI list section
            const productNameSpan = document.getElementById('selectedProductName');
            if (productNameSpan) {
                productNameSpan.textContent = productName;
            }

            // Fetch PPIs for this product via AJAX
            fetch(`{{ route('spi_get_ppi_list_for_product', $warehouse_code) }}?product_id=${productId}&spi_id=${spiId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (ppiListLoading) {
                    ppiListLoading.style.display = 'none';
                }

                if (data.success && data.ppis && data.ppis.length > 0) {
                    // Dispatch event to render PPI list
                    window.dispatchEvent(new CustomEvent('renderPpiList', {
                        detail: { 
                            ppis: data.ppis,
                            is_subordinate_manager: data.is_subordinate_manager,
                            is_managers_as_sm: data.is_managers_as_sm
                        }
                    }));
                    if (ppiListEmpty) {
                        ppiListEmpty.style.display = 'none';
                    }
                } else {
                    if (ppiListEmpty) {
                        ppiListEmpty.style.display = 'block';
                    }
                    if (ppiListBody) {
                        ppiListBody.innerHTML = '';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (ppiListLoading) {
                    ppiListLoading.style.display = 'none';
                }
                if (ppiListEmpty) {
                    ppiListEmpty.style.display = 'block';
                }
            });
        } else {
            // Clear PPI list when product is deselected
            const ppiListSection = document.querySelector('[data-ppi-list-section]');
            if (ppiListSection) {
                ppiListSection.style.display = 'none';
            }
        }
    });
});
</script>
