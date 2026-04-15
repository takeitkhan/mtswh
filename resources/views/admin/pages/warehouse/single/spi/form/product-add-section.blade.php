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
    const productSelect = document.getElementById('spiProductSelect');

    // When product is selected, trigger event to show PPI list
    productSelect.addEventListener('change', function() {
        if (this.value) {
            const productName = this.options[this.selectedIndex].text;
            
            // Dispatch custom event to show PPI list section
            const event = new CustomEvent('productSelected', {
                detail: {
                    productId: this.value,
                    productName: productName
                }
            });
            window.dispatchEvent(event);
        }
    });
});
</script>
