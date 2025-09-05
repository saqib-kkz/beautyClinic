@extends('layouts.main')

@section('page_style')
    <style>
        .card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .products-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .product-row {
            background-color: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            margin-bottom: 10px;
        }
        .remove-product {
            border: none;
            background: none;
            color: #dc3545;
            font-size: 1.2em;
        }
        .stock-info {
            font-size: 0.85em;
            color: #6c757d;
        }
        .client-history {
            background-color: #e7f3ff;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }
        .btn-disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
@endsection

@section('title')
    Treatments
@endsection

@section('sub-title')
    Add New Treatment
@endsection

@section('page')
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Treatment Information</h5>

                        <form id="treatmentForm">
                            @csrf
                            
                            <div class="row">
                                <!-- Client Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id" class="form-label">Client *</label>
                                        <select class="form-select" id="client_id" name="client_id" required>
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" data-contact="{{ $client->contact_number }}">
                                                    {{ $client->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    
                                    <!-- Client History (will show when client is selected) -->
                                    <div id="clientHistory" class="client-history" style="display: none;">
                                        <h6>Recent Treatments</h6>
                                        <div id="clientHistoryContent"></div>
                                    </div>
                                </div>

                                <!-- Treatment Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="treatment_date" class="form-label">Treatment Date *</label>
                                        <input type="date" class="form-control" id="treatment_date" name="treatment_date" 
                                               value="{{ date('Y-m-d') }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Therapist Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="therapist_name" class="form-label">Therapist Name *</label>
                                        <input type="text" class="form-control" id="therapist_name" name="therapist_name" 
                                               placeholder="Enter therapist name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <!-- Treatment Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="treatment_name" class="form-label">Treatment Name *</label>
                                        <input type="text" class="form-control" id="treatment_name" name="treatment_name" 
                                               placeholder="Enter treatment name" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Treatment Reason -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="treatment_reason" class="form-label">Treatment Reason</label>
                                        <textarea class="form-control" id="treatment_reason" name="treatment_reason" 
                                                  rows="3" placeholder="Enter reason for treatment (optional)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Used Section -->
                            <div class="products-section">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6>Products Used in Treatment</h6>
                                    <button type="button" class="btn btn-outline-primary" id="addProductBtn">
                                        <i class="bi bi-plus"></i> Add Product
                                    </button>
                                </div>

                                <div id="productsContainer">
                                    <!-- Product rows will be added here -->
                                </div>

                                <div id="noProductsMessage" class="text-center text-muted py-3">
                                    No products added yet. Click "Add Product" to get started.
                                </div>
                            </div>

                            <div class="row">
                                <!-- Notes -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" 
                                                  rows="3" placeholder="Additional notes (optional)"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-check-circle"></i> Save Treatment
                                </button>
                                <a href="{{ route('treatments.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_script')
    <script>
        let availableProducts = @json($products);
        let usedProductIds = [];

        $(document).ready(function() {
            // Add first product row
            addProductRow();

            // Client selection change
            $('#client_id').on('change', function() {
                const clientId = $(this).val();
                if (clientId) {
                    loadClientHistory(clientId);
                } else {
                    $('#clientHistory').hide();
                }
            });

            // Add product button
            $('#addProductBtn').on('click', function() {
                addProductRow();
            });

            // Form submission
            $('#treatmentForm').on('submit', function(e) {
                e.preventDefault();
                submitTreatment();
            });
        });

        function addProductRow() {
            const productRow = $(`
                <div class="product-row">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Product *</label>
                            <select class="form-select product-select" name="products[${Date.now()}][product_id]" required>
                                <option value="">Select Product</option>
                            </select>
                            <div class="stock-info mt-1"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity Used *</label>
                            <input type="number" class="form-control quantity-input" 
                                   name="products[${Date.now()}][quantity_used]" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unit Price</label>
                            <input type="text" class="form-control unit-price" readonly>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger remove-product" 
                                    onclick="removeProductRow(this)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);

            $('#productsContainer').append(productRow);
            updateProductOptions();
            updateNoProductsMessage();

            // Bind events for the new row
            productRow.find('.product-select').on('change', function() {
                const selectedProductId = $(this).val();
                
                // Check if product is already selected in another row
                if (selectedProductId && isProductAlreadySelected(selectedProductId, this)) {
                    Swal.fire({
                        icon: 'question',
                        title: 'Product Already Selected',
                        text: 'This product is already selected. Would you like to combine the quantities?',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, combine',
                        cancelButtonText: 'Cancel',
                        confirmButtonColor: '#28a745'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            combineProductQuantities(selectedProductId, this);
                        } else {
                            $(this).val('');
                            updateProductInfo(this);
                        }
                    });
                    return;
                }
                
                updateProductInfo(this);
                validateQuantity(productRow.find('.quantity-input'));
            });

            productRow.find('.quantity-input').on('input', function() {
                validateQuantity(this);
            });
        }

        function removeProductRow(button) {
            $(button).closest('.product-row').remove();
            updateUsedProducts();
            updateProductOptions();
            updateNoProductsMessage();
        }

        function updateProductOptions() {
            updateUsedProducts();
            
            $('.product-select').each(function() {
                const currentValue = $(this).val();
                const options = ['<option value="">Select Product</option>'];
                
                availableProducts.forEach(product => {
                    // Allow reselection of current product or products not used elsewhere
                    if (!usedProductIds.includes(product.id.toString()) || product.id.toString() === currentValue) {
                        const stockStatus = product.stock_quantity <= 5 ? ' (Low Stock)' : '';
                        options.push(
                            `<option value="${product.id}" data-stock="${product.stock_quantity}" 
                                     data-price="${product.price}" data-unit="${product.unit_type}">
                                ${product.name}${stockStatus}
                            </option>`
                        );
                    }
                });
                
                $(this).html(options.join(''));
                
                if (currentValue) {
                    $(this).val(currentValue);
                }
            });
        }

        function updateUsedProducts() {
            usedProductIds = [];
            $('.product-select').each(function() {
                const value = $(this).val();
                if (value) {
                    usedProductIds.push(value);
                }
            });
        }

        function updateProductInfo(selectElement) {
            const $select = $(selectElement);
            const $row = $select.closest('.product-row');
            const $stockInfo = $row.find('.stock-info');
            const $unitPrice = $row.find('.unit-price');
            const $quantityInput = $row.find('.quantity-input');

            if ($select.val()) {
                const option = $select.find('option:selected');
                const stock = option.data('stock');
                const price = option.data('price');
                const unit = option.data('unit');

                $stockInfo.html(`Available: ${stock} ${unit}(s)`);
                $unitPrice.val(`AED ${parseFloat(price).toFixed(2)}`);
                $quantityInput.attr('max', stock);
                
                if (stock <= 5) {
                    $stockInfo.addClass('text-warning').append(' <strong>(Low Stock!)</strong>');
                }
            } else {
                $stockInfo.empty();
                $unitPrice.val('');
                $quantityInput.removeAttr('max');
            }

            updateUsedProducts();
            updateProductOptions();
        }

        function validateQuantity(input) {
            const $input = $(input);
            const $row = $input.closest('.product-row');
            const $select = $row.find('.product-select');
            const maxStock = $select.find('option:selected').data('stock');
            const quantity = parseInt($input.val());

            if (quantity && maxStock && quantity > maxStock) {
                $input.addClass('is-invalid');
                $input.siblings('.invalid-feedback').text(`Maximum available: ${maxStock}`);
            } else {
                $input.removeClass('is-invalid');
            }
        }

        function updateNoProductsMessage() {
            if ($('#productsContainer .product-row').length === 0) {
                $('#noProductsMessage').show();
            } else {
                $('#noProductsMessage').hide();
            }
        }

        function isProductAlreadySelected(productId, currentSelect) {
            let isSelected = false;
            $('.product-select').not(currentSelect).each(function() {
                if ($(this).val() === productId) {
                    isSelected = true;
                    return false; // Break the loop
                }
            });
            return isSelected;
        }

        function combineProductQuantities(productId, currentSelect) {
            let existingRow = null;
            let existingQuantity = 0;
            let newQuantity = 1; // Default quantity
            
            // Find existing row with same product
            $('.product-select').not(currentSelect).each(function() {
                if ($(this).val() === productId) {
                    existingRow = $(this).closest('.product-row');
                    existingQuantity = parseInt(existingRow.find('.quantity-input').val()) || 0;
                    return false;
                }
            });
            
            if (existingRow) {
                // Get new quantity from current row if set
                const currentRow = $(currentSelect).closest('.product-row');
                const currentQuantity = parseInt(currentRow.find('.quantity-input').val()) || 1;
                newQuantity = currentQuantity;
                
                // Combine quantities
                const combinedQuantity = existingQuantity + newQuantity;
                existingRow.find('.quantity-input').val(combinedQuantity);
                
                // Validate combined quantity doesn't exceed stock
                const maxStock = existingRow.find('.product-select option:selected').data('stock');
                if (combinedQuantity > maxStock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Stock Limitation',
                        text: `Combined quantity (${combinedQuantity}) exceeds available stock (${maxStock}). Setting to maximum available.`,
                        confirmButtonColor: '#ffc107'
                    });
                    existingRow.find('.quantity-input').val(maxStock);
                }
                
                // Remove current row
                currentRow.remove();
                updateUsedProducts();
                updateProductOptions();
                updateNoProductsMessage();
                
                // Trigger validation on existing row
                validateQuantity(existingRow.find('.quantity-input'));
            }
        }

        function loadClientHistory(clientId) {
            $.ajax({
                url: `{{ route('treatments.api.client-treatments', '') }}/${clientId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let html = '<div class="row">';
                        response.data.slice(0, 3).forEach(treatment => {
                            const date = new Date(treatment.treatment_date).toLocaleDateString();
                            html += `
                                <div class="col-12 mb-2">
                                    <small><strong>${date}</strong>: ${treatment.treatment_name}</small>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#clientHistoryContent').html(html);
                        $('#clientHistory').show();
                    }
                },
                error: function(xhr) {
                    console.error('Error loading client history:', xhr);
                }
            });
        }

        function submitTreatment() {
            const $submitBtn = $('#submitBtn');
            const $form = $('#treatmentForm');
            
            // Disable submit button
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            // Prepare form data
            const formData = new FormData($form[0]);
            
            // Add products data and handle duplicates
            const productsMap = new Map();
            $('#productsContainer .product-row').each(function() {
                const productId = $(this).find('.product-select').val();
                const quantity = parseInt($(this).find('.quantity-input').val());
                
                if (productId && quantity) {
                    const id = parseInt(productId);
                    if (productsMap.has(id)) {
                        // Combine quantities for duplicate products
                        productsMap.set(id, productsMap.get(id) + quantity);
                    } else {
                        productsMap.set(id, quantity);
                    }
                }
            });

            if (productsMap.size === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Products',
                    text: 'Please add at least one product to the treatment.',
                    confirmButtonColor: '#3085d6'
                });
                $submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Treatment');
                return;
            }

            // Convert map to products array
            const products = [];
            productsMap.forEach((quantity, productId) => {
                products.push({
                    product_id: productId,
                    quantity_used: quantity
                });
            });

            $.ajax({
                url: '{{ route("treatments.store") }}',
                method: 'POST',
                data: {
                    ...Object.fromEntries(formData),
                    products: products
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Treatment saved successfully!',
                            confirmButtonColor: '#28a745',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.href = '{{ route("treatments.index") }}';
                            }
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const $field = $(`[name="${field}"]`);
                            $field.addClass('is-invalid');
                            $field.siblings('.invalid-feedback').text(errors[field][0]);
                        });
                        
                        // Show validation error summary
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please check the form fields and try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        const message = xhr.responseJSON?.message || 'An error occurred while saving the treatment.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Treatment');
                }
            });
        }
    </script>
@endsection