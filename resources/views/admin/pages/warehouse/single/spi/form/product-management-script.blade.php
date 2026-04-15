<!-- SPI Product Management Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find the main form for SPI
        const mainForm = document.querySelector('form[method="post"]');
        
        if (mainForm) {
            // Handle form submission to collect product data
            mainForm.addEventListener('submit', function(e) {
                const productCards = document.querySelectorAll('.product-card');
                
                console.log('Form submitted with ' + productCards.length + ' products');

                // Collect all product data from cards and add to form
                productCards.forEach(card => {
                    const qtyInput = card.querySelector('.product-qty');
                    const productId = card.getAttribute('data-product-id');
                    
                    if (productId && qtyInput) {
                        // Create or update hidden input for this product
                        let productInput = mainForm.querySelector(`input[name="product[${productId}][qty]"]`);
                        
                        if (!productInput) {
                            // Create new hidden input
                            productInput = document.createElement('input');
                            productInput.type = 'hidden';
                            productInput.name = `product[${productId}][qty]`;
                            productInput.value = qtyInput.value;
                            mainForm.appendChild(productInput);
                        } else {
                            // Update existing input
                            productInput.value = qtyInput.value;
                        }
                    }
                });

                console.log('Product data prepared for submission');
            });

            // Validate that at least one product is added (optional)
            // You can uncomment this if you want to require products
            /*
            mainForm.addEventListener('submit', function(e) {
                const productCards = document.querySelectorAll('.product-card');
                if (productCards.length === 0) {
                    e.preventDefault();
                    showAlert('Please add at least one product to SPI', 'warning');
                }
            });
            */
        }

        // Print functionality
        document.querySelectorAll('.ppi_print_data').forEach(btn => {
            btn.addEventListener('click', function() {
                window.print();
            });
        });

        // Prevent accidental navigation when products are added
        let productsAdded = false;
        document.addEventListener('addProductToSpi', function() {
            productsAdded = true;
        });

        window.addEventListener('beforeunload', function(e) {
            if (productsAdded && !mainForm.querySelector('input[name="id"]')) {
                // Only show warning if products are added but SPI not saved
                e.preventDefault();
                e.returnValue = '';
            }
        });

        console.log('SPI Product Management initialized');
    });

    // Global alert function
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Add to top of main content
        const mainContent = document.querySelector('.container-fluid') || document.body;
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>
