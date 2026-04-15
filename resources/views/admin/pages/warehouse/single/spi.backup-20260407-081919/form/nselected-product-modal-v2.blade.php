<!-- Modern Bulk Add Modal for SPI Products -->
<div class="modal fade" id="selectedProductInfoOpenModal" tabindex="-1" aria-labelledby="selectedProductInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="selectedProductInfoLabel">
                    <i class="fa fa-plus-circle me-2"></i>Bulk Add Products
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <!-- Step 1: Search Products -->
                <div id="step1-search">
                    <h6 class="mb-3">
                        <span class="badge bg-primary">Step 1</span> Search & Select Products
                    </h6>
                    
                    <!-- Search Box -->
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="productSearchInput" placeholder="Search by Product Name, Code, PPI ID...">
                        <button class="btn btn-primary" type="button" id="productSearchBtn">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>

                    <!-- Search Results -->
                    <div id="searchResults" style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px; display: none;">
                        <div id="resultsContainer"></div>
                    </div>
                </div>

                <!-- Step 2: Selected Products List with Quantities -->
                <div id="step2-quantities" class="mt-4" style="display: none;">
                    <h6 class="mb-3">
                        <span class="badge bg-success">Step 2</span> Enter Quantities
                    </h6>

                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>PPI ID</th>
                                    <th>Stock Available</th>
                                    <th>Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="selectedProductsTable">
                                <!-- Products will be added here -->
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mb-3">
                        <strong>Total Quantity Selected:</strong> 
                        <span id="totalQtyDisplay" class="badge bg-primary fs-6">0</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="proceedToQtyBtn" style="display: none;">
                    <i class="fa fa-arrow-right me-2"></i>Enter Quantities
                </button>
                <button type="button" class="btn btn-success" id="generateProductsBtn" style="display: none;">
                    <i class="fa fa-check me-2"></i>Generate Products
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // State management
    const modalState = {
        searchResults: [],
        selectedProducts: [],
        currentStep: 1
    };

    // Step 1: Search Products
    $(document).on('click', '#productSearchBtn', function() {
        const searchQuery = $('#productSearchInput').val().trim();
        
        if (!searchQuery) {
            alert('Please enter a search term');
            return;
        }

        const warehouseId = "{{$warehouse_id}}";
        const spiProject = "{{$spi_project}}";

        $.ajax({
            url: "{{route('spi_search_products')}}",
            method: 'GET',
            data: {
                search: searchQuery,
                warehouse_id: warehouseId,
                project: spiProject
            },
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    displaySearchResults(response.data);
                } else {
                    alert('No products found');
                    $('#searchResults').hide();
                }
            },
            error: function() {
                alert('Error searching products');
            }
        });
    });

    // Display search results
    function displaySearchResults(products) {
        const html = products.map((product, index) => `
            <div class="card mb-2 cursor-pointer product-result-card" data-index="${index}">
                <div class="card-body p-2">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <strong>${product.product_name}</strong><br>
                            <small class="text-muted">
                                Code: ${product.product_code} | PPI: ${product.ppi_id}
                            </small>
                        </div>
                        <div class="col-md-3">
                            <small>Stock: <strong>${product.stock_in_hand}</strong></small>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="button" class="btn btn-sm btn-primary select-product-btn" data-index="${index}">
                                <i class="fa fa-plus"></i> Select
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        $('#resultsContainer').html(html);
        $('#searchResults').show();
        modalState.searchResults = products;
    }

    // Select product
    $(document).on('click', '.select-product-btn', function() {
        const index = $(this).data('index');
        const product = modalState.searchResults[index];

        // Check if already selected
        if (!modalState.selectedProducts.find(p => p.ppi_product_id === product.ppi_product_id)) {
            modalState.selectedProducts.push({
                product_name: product.product_name,
                ppi_id: product.ppi_id,
                ppi_product_id: product.ppi_product_id,
                bundle_id: product.bundle_id,
                stock_in_hand: product.stock_in_hand,
                from_warehouse: product.warehouse_id,
                landed_project: product.project,
                original_project: "{{$original_project}}",
                qty: 0
            });

            toastr.success('Product added to selection');
            updateSelectedProductsDisplay();

            // Show proceed button if we have selections
            if (modalState.selectedProducts.length > 0) {
                $('#proceedToQtyBtn').show();
            }
        } else {
            alert('Product already selected');
        }
    });

    // Update quantities table
    function updateSelectedProductsDisplay() {
        if (modalState.selectedProducts.length === 0) {
            $('#step2-quantities').hide();
            return;
        }

        const html = modalState.selectedProducts.map((product, index) => `
            <tr>
                <td>${product.product_name}</td>
                <td><small>${product.ppi_id}</small></td>
                <td><span class="badge bg-info">${product.stock_in_hand}</span></td>
                <td>
                    <input type="number" min="0" max="${product.stock_in_hand}" value="${product.qty}" 
                           class="form-control form-control-sm qty-input" data-index="${index}" 
                           style="width: 70px;">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-product-btn" data-index="${index}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        $('#selectedProductsTable').html(html);
        updateTotalQty();
    }

    // Update total quantity
    $(document).on('input', '.qty-input', function() {
        const index = $(this).data('index');
        const qty = parseInt($(this).val()) || 0;
        modalState.selectedProducts[index].qty = qty;
        updateTotalQty();
    });

    function updateTotalQty() {
        const total = modalState.selectedProducts.reduce((sum, p) => sum + p.qty, 0);
        $('#totalQtyDisplay').text(total);
    }

    // Remove product from selection
    $(document).on('click', '.remove-product-btn', function() {
        const index = $(this).data('index');
        modalState.selectedProducts.splice(index, 1);
        updateSelectedProductsDisplay();
        
        if (modalState.selectedProducts.length === 0) {
            $('#proceedToQtyBtn').hide();
        }
    });

    // Proceed to quantities
    $(document).on('click', '#proceedToQtyBtn', function() {
        $('#step1-search').hide();
        $('#step2-quantities').show();
        $('#proceedToQtyBtn').hide();
        $('#generateProductsBtn').show();
        updateSelectedProductsDisplay();
    });

    // Generate products
    $(document).on('click', '#generateProductsBtn', function() {
        // Filter products with qty > 0
        const productsToAdd = modalState.selectedProducts.filter(p => p.qty > 0);

        if (productsToAdd.length === 0) {
            alert('Please enter quantity for at least one product');
            return;
        }

        // Generate sections
        generateProductSections(productsToAdd);

        // Close modal
        $('#selectedProductInfoOpenModal').modal('hide');
        toastr.success('Products added successfully');

        // Reset modal state
        resetModalState();
    });

    // Generate product sections in main form
    function generateProductSections(products) {
        const container = $('#spi_product_section');

        products.forEach((product) => {
            const maxId = Math.max(0, ...$('.colgroup[data-id]').map(function() {
                return parseInt($(this).data('id')) || 0;
            }).get());

            const newId = maxId + 1;

            const html = `
                <div class="col-md-3 mb-2 colgroup prb${newId}" data-id="${newId}">
                    <div class="card border-1">
                        <div class="card-header p-1">
                            <a href="javascript:void(0);" class="remove-product d-inline-block valign-text-bottom me-2 float-end" title="Remove field"><i class="fa fa-times"></i></a>
                        </div>
                        <div class="card-body">
                            <!-- Product Name -->
                            <div class="form-group">
                                <label for="product">Select Product</label>
                                <input type="hidden" name="product[${newId}][product_id]" value="">
                                <div class="alert alert-info py-2 mb-2">${product.product_name}</div>
                            </div>

                            <!-- Check Stock Button -->
                            <div class="form-group pb-2">
                                <label>&nbsp;</label>
                                <button type="button" class="selectedProductInfo btn btn-sm btn-primary py-0" data-row-id="${newId}">
                                    Check Stock
                                </button>
                            </div>

                            <!-- Hidden Fields -->
                            <div class="ppiInformation${newId}">
                                <div class="ppi_id_append">
                                    <input type="hidden" value="${product.ppi_id}" name="product[${newId}][ppi_id]" />
                                    <input type="hidden" value="${product.ppi_product_id}" name="product[${newId}][ppi_product_id]" />
                                    <input type="hidden" value="${product.from_warehouse}" name="product[${newId}][from_warehouse]" />
                                    <input type="hidden" value="${product.landed_project}" name="product[${newId}][landed_project]" />
                                    <input type="hidden" value="${product.original_project}" name="product[${newId}][originalProject]" />
                                    ${product.bundle_id ? `<input type="hidden" value="${product.bundle_id}" name="product[${newId}][bundle_id]" />` : ''}
                                </div>
                            </div>

                            <!-- QTY -->
                            <div class="form-group" id="regular_qty" data-id="${newId}">
                                <label for="qty">QTY</label>
                                <input type="number" min="1" name="product[${newId}][qty]" id="qty" class="form-control form-control-sm" value="${product.qty}" required>
                            </div>

                            <!-- Unit Price -->
                            <div class="form-group" id="single_product_unit_price" data-id="${newId}">
                                <label for="single_product_unit_price">Unit Price</label>
                                <input step="any" type="number" name="product[${newId}][unit_price]" id="single_product_unit_price" class="form-control form-control-sm unit_price" value="0">
                            </div>

                            <!-- Total Price -->
                            <div class="form-group" id="total_price" data-id="${newId}">
                                <label for="price">Total Price</label>
                                <input step="any" type="number" name="product[${newId}][price]" id="price" class="form-control form-control-sm total_price" readonly value="0">
                            </div>

                            <!-- Note -->
                            <div class="form-group">
                                <label for="note">Note</label>
                                <textarea class="form-control form-control-sm" name="product[${newId}][note]" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.append(html);
            attachPriceCalculationListeners();
        });
    }

    // Price calculation
    function attachPriceCalculationListeners() {
        $(document).on('keyup change', '#regular_qty #qty, #single_product_unit_price #single_product_unit_price', function() {
            const dataId = $(this).parents('.colgroup').attr('data-id');
            const qty = parseFloat($(".prb" + dataId + " #regular_qty #qty").val() || 0);
            const price = parseFloat($(".prb" + dataId + " #single_product_unit_price #single_product_unit_price").val() || 0);
            const total = (qty * price).toFixed(2);
            $(".prb" + dataId + " .total_price").val(total);
        });
    }

    // Reset modal state
    function resetModalState() {
        modalState.searchResults = [];
        modalState.selectedProducts = [];
        modalState.currentStep = 1;

        $('#productSearchInput').val('');
        $('#searchResults').hide();
        $('#step1-search').show();
        $('#step2-quantities').hide();
        $('#proceedToQtyBtn').hide();
        $('#generateProductsBtn').hide();
        $('#selectedProductsTable').empty();
        $('#totalQtyDisplay').text('0');
    }

    // Reset when modal closes
    $('#selectedProductInfoOpenModal').on('hidden.bs.modal', function() {
        resetModalState();
    });
</script>

<style>
    .product-result-card {
        cursor: pointer;
        transition: all 0.3s;
    }

    .product-result-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background-color: #f8f9fa;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
