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
    Invoices
@endsection

@section('sub-title')
    Add New Invoice
@endsection

@section('page')
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Invoice Information</h5>

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
                                        <select class="form-select" id="therapist_name" name="therapist_name" required>
                                            <option value="">Select Therapist</option>
                                            @foreach($staff as $therapist)
                                                <option value="{{ $therapist->name }}">
                                                    {{ $therapist->name }} ({{ ucfirst($therapist->role) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <!-- Treatment Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="treatment_name" class="form-label">Treatment Name *</label>
                                        <input type="text" class="form-control" id="treatment_name" name="treatment_name" 
                                               placeholder="Enter or search treatment name" required autocomplete="off">
                                        <input type="hidden" id="treatment_type_id" name="treatment_type_id" value="">
                                        <div class="invalid-feedback"></div>
                                        
                                        <!-- Suggestions dropdown -->
                                        <div id="treatment_suggestions" class="dropdown-menu w-100" style="display: none; max-height: 200px; overflow-y: auto;">
                                            <!-- Suggestions will be populated here -->
                                        </div>
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

                            <!-- Payment Information Section -->
                            <div class="payment-section" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-top: 20px;">
                                <h6>Payment Information</h6>
                                
                                <div class="row">
                                    <!-- Treatment Amount -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="treatment_amount" class="form-label">Treatment Amount (AED) *</label>
                                            <input type="number" class="form-control" id="treatment_amount" name="treatment_amount" 
                                                   step="0.01" min="0" placeholder="0.00" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <!-- Discount -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="discount" class="form-label">Discount (AED) *</label>
                                            <input type="number" class="form-control" id="discount" name="discount" 
                                                   step="0.01" min="0" value="0" placeholder="0.00" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <!-- Payment Type -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="payment_type" class="form-label">Payment Type *</label>
                                            <select class="form-select" id="payment_type" name="payment_type" required>
                                                <option value="">Select Payment Type</option>
                                                <option value="cash">Cash</option>
                                                <option value="card">Card</option>
                                                <option value="tabby">Tabby</option>
                                                <option value="tamara">Tamara</option>
                                                <option value="bank_transfer">Bank Transfer</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Calculation Summary -->
                                <div class="calculation-summary" style="background-color: white; padding: 15px; border-radius: 6px; margin-top: 15px;">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Products Subtotal</label>
                                            <div class="form-control-plaintext" id="products_subtotal">AED 0.00</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Subtotal (Products + Treatment)</label>
                                            <div class="form-control-plaintext" id="subtotal">AED 0.00</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">VAT Amount (5%)</label>
                                            <div class="form-control-plaintext" id="vat_amount">AED 0.00</div>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label"><strong>Total Amount</strong></label>
                                            <div class="form-control-plaintext text-success" id="total_amount" style="font-size: 1.2em; font-weight: bold;">AED 0.00</div>
                                        </div>
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

            // Payment calculation handlers
            $('#treatment_amount, #discount').on('input', function() {
                calculateTotals();
            });

            // Calculate totals when products change
            $(document).on('change', '.product-select, .quantity-input', function() {
                calculateTotals();
            });

            // Treatment name autocomplete
            let searchTimeout;
            $('#treatment_name').on('input', function() {
                const searchTerm = $(this).val().trim();
                clearTimeout(searchTimeout);
                
                if (searchTerm.length >= 2) {
                    searchTimeout = setTimeout(() => {
                        searchTreatmentTypes(searchTerm);
                    }, 300);
                } else {
                    hideTreatmentSuggestions();
                }
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#treatment_name, #treatment_suggestions').length) {
                    hideTreatmentSuggestions();
                }
            });

            // Handle suggestion selection
            $(document).on('click', '.treatment-suggestion', function() {
                const treatmentId = $(this).data('id');
                const treatmentName = $(this).data('name');
                const treatmentPrice = $(this).data('price');

                $('#treatment_name').val(treatmentName);
                $('#treatment_type_id').val(treatmentId);

                // Auto-populate treatment amount if price is available
                if (treatmentPrice && treatmentPrice !== '') {
                    $('#treatment_amount').val(parseFloat(treatmentPrice).toFixed(2));
                    calculateTotals(); // Recalculate totals
                }

                hideTreatmentSuggestions();
            });
        });

        function addProductRow() {
            const productRow = $(`
                <div class="product-row">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Product *</label>
                            <select class="form-select product-select" required>
                                <option value="">Select Product</option>
                            </select>
                            <div class="stock-info mt-1"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity Used *</label>
                            <div class="input-group">
                                <input type="number" class="form-control quantity-input" min="0.01" step="0.01" required>
                                <span class="input-group-text quantity-unit">units</span>
                            </div>
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
                calculateTotals();
            });
        }

        function removeProductRow(button) {
            $(button).closest('.product-row').remove();
            updateUsedProducts();
            updateProductOptions();
            updateNoProductsMessage();
            calculateTotals();
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
                        const isVial = product.unit_type && product.unit_type.toLowerCase().includes('vial');
                        const pricePerMl = product.price_per_ml || 0;
                        options.push(
                            `<option value="${product.id}" data-stock="${product.stock_quantity}"
                                     data-price="${product.price}" data-price-per-ml="${pricePerMl}"
                                     data-unit="${product.unit_type}" data-is-vial="${isVial}">
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
                const pricePerMl = option.data('price-per-ml');
                const unit = option.data('unit');
                const isVial = option.data('is-vial');
                const $quantityUnit = $row.find('.quantity-unit');

                // Update unit display
                if (isVial) {
                    $quantityUnit.text('ml');
                    $stockInfo.html(`Available: ${stock} ml`);
                    $unitPrice.val(`AED ${parseFloat(pricePerMl || price).toFixed(2)} per ml`);
                } else {
                    $quantityUnit.text('units');
                    $stockInfo.html(`Available: ${stock} ${unit}(s)`);
                    $unitPrice.val(`AED ${parseFloat(price).toFixed(2)} per unit`);
                }

                $quantityInput.attr('max', stock);

                if (stock <= 5) {
                    $stockInfo.addClass('text-warning').append(' <strong>(Low Stock!)</strong>');
                }
            } else {
                $stockInfo.empty();
                $unitPrice.val('');
                $quantityInput.removeAttr('max');
                $row.find('.quantity-unit').text('units');
            }

            updateUsedProducts();
            updateProductOptions();
        }

        function validateQuantity(input) {
            const $input = $(input);
            const $row = $input.closest('.product-row');
            const $select = $row.find('.product-select');
            const maxStock = $select.find('option:selected').data('stock');
            const quantity = parseFloat($input.val());

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
                    existingQuantity = parseFloat(existingRow.find('.quantity-input').val()) || 0;
                    return false;
                }
            });
            
            if (existingRow) {
                // Get new quantity from current row if set
                const currentRow = $(currentSelect).closest('.product-row');
                const currentQuantity = parseFloat(currentRow.find('.quantity-input').val()) || 1;
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

        function calculateTotals() {
            let productsSubtotal = 0;
            
            // Calculate products subtotal
            $('#productsContainer .product-row').each(function() {
                const $row = $(this);
                const $select = $row.find('.product-select');
                const $quantityInput = $row.find('.quantity-input');
                
                if ($select.val() && $quantityInput.val()) {
                    const option = $select.find('option:selected');
                    const isVial = option.data('is-vial');
                    const price = parseFloat(option.data('price')) || 0;
                    const pricePerMl = parseFloat(option.data('price-per-ml')) || 0;
                    const unitPrice = isVial && pricePerMl ? pricePerMl : price;
                    const quantity = parseFloat($quantityInput.val()) || 0;
                    productsSubtotal += (unitPrice * quantity);
                }
            });
            
            const treatmentAmount = parseFloat($('#treatment_amount').val()) || 0;
            const discount = parseFloat($('#discount').val()) || 0;
            
            // Calculate amounts
            const subtotal = productsSubtotal + treatmentAmount;
            const afterDiscount = subtotal - discount;
            const vatAmount = afterDiscount * 0.05;
            const totalAmount = afterDiscount + vatAmount;
            
            // Update display
            $('#products_subtotal').text('AED ' + productsSubtotal.toFixed(2));
            $('#subtotal').text('AED ' + subtotal.toFixed(2));
            $('#vat_amount').text('AED ' + vatAmount.toFixed(2));
            $('#total_amount').text('AED ' + totalAmount.toFixed(2));
        }

        function searchTreatmentTypes(searchTerm) {
            $.ajax({
                url: '{{ route("treatments.api.treatment-types") }}',
                method: 'GET',
                data: { search: searchTerm },
                success: function(response) {
                    if (response.success) {
                        showTreatmentSuggestions(response.data, searchTerm);
                    }
                },
                error: function() {
                    hideTreatmentSuggestions();
                }
            });
        }

        function showTreatmentSuggestions(suggestions, searchTerm) {
            const $dropdown = $('#treatment_suggestions');
            let html = '';

            // Add existing suggestions
            suggestions.forEach(function(suggestion) {
                const priceText = suggestion.price ? `AED ${parseFloat(suggestion.price).toFixed(2)}` : 'No price set';
                html += `<div class="dropdown-item treatment-suggestion" data-id="${suggestion.id}" data-name="${suggestion.name}" data-price="${suggestion.price || ''}" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span>${suggestion.name}</span>
                            <br><small class="text-muted">Price: ${priceText}</small>
                        </div>
                        <small class="text-muted">${suggestion.usage_count} times</small>
                    </div>
                </div>`;
            });

            // Add option to create new if no exact match
            const exactMatch = suggestions.find(s => s.name.toLowerCase() === searchTerm.toLowerCase());
            if (!exactMatch && searchTerm.length >= 3) {
                html += `<div class="dropdown-divider"></div>
                <div class="dropdown-item treatment-suggestion" data-id="0" data-name="${searchTerm}" data-price="" style="cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2 text-success"></i>
                        <span>Create new: "<strong>${searchTerm}</strong>"</span>
                    </div>
                </div>`;
            }

            $dropdown.html(html).show();
        }

        function hideTreatmentSuggestions() {
            $('#treatment_suggestions').hide();
        }

        function submitTreatment() {
            const $submitBtn = $('#submitBtn');
            const $form = $('#treatmentForm');
            
            // Disable submit button
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            // Prepare form data (excluding product inputs to avoid duplication)
            const formData = {
                client_id: $('#client_id').val(),
                treatment_date: $('#treatment_date').val(),
                therapist_name: $('#therapist_name').val(),
                treatment_name: $('#treatment_name').val(),
                treatment_type_id: $('#treatment_type_id').val(),
                treatment_reason: $('#treatment_reason').val(),
                notes: $('#notes').val(),
                treatment_amount: $('#treatment_amount').val(),
                discount: $('#discount').val(),
                payment_type: $('#payment_type').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            // Add products data and handle duplicates
            const productsMap = new Map();
            $('#productsContainer .product-row').each(function() {
                const productId = $(this).find('.product-select').val();
                const quantity = parseFloat($(this).find('.quantity-input').val());
                
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
                    ...formData,
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