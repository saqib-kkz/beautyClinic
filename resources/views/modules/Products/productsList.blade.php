@extends('layouts.main')

@section('page_style')
    <link href="{{ getadminasset('vendor/simple-datatables/style.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.6/datatables.min.css" rel="stylesheet">
    <style>
        .modal-lg {
            max-width: 800px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .btn:disabled {
            cursor: not-allowed;
        }
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .sort-icon {
            font-size: 0.8em;
            margin-left: 5px;
        }
        .sort-asc .sort-icon::before {
            content: "\F12C"; /* bi-arrow-up */
        }
        .sort-desc .sort-icon::before {
            content: "\F12F"; /* bi-arrow-down */
        }
        .sortable:not(.sort-asc):not(.sort-desc) .sort-icon::before {
            content: "\F12E"; /* bi-arrow-down-up */
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>
@endsection
@section('title')
    Products
@endsection
@section('sub-title')
    Products List
@endsection
@section('page')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                {!! displayAlert() !!}
                <div class="card">
                    <div class="card-header align-items-center justify-content-between d-flex py-3">
                        <h5 class="card-title">All Products</h5>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search products...">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <button class="btn btn-outline-secondary" type="button" id="refreshBtn" title="Refresh">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table" id="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="sortable" data-sort="id">
                                        # <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th scope="col" class="sortable" data-sort="name">
                                        Name <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th scope="col" class="sortable" data-sort="price">
                                        Price <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th scope="col" class="sortable" data-sort="stock_quantity">
                                        Quantity <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th scope="col" class="sortable" data-sort="unit_type">
                                        Category <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th scope="col" class="sortable" data-sort="is_active">
                                        Status <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                        <div id="no-data" class="text-center py-4" style="display: none;">
                            <p class="text-muted">No products found. <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">Add First Product</button></p>
                        </div>
                        
                        <!-- Pagination Controls -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="d-flex align-items-center gap-2">
                                <label for="perPageSelect" class="form-label mb-0">Show:</label>
                                <select class="form-select form-select-sm" id="perPageSelect" style="width: auto;">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-muted" id="paginationInfo">Showing 0 to 0 of 0 entries</span>
                            </div>
                            <nav aria-label="Products pagination">
                                <ul class="pagination pagination-sm mb-0" id="pagination">
                                    <!-- Pagination will be generated here -->
                                </ul>
                            </nav>
                        </div>
                        <div class="modal fade" id="editModel" tabindex="-1" aria-labelledby="exampleModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Edit Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="edit_form">
                                            <input type="hidden" id="edit_modal_id" value="">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="edit_name">Name</label>
                                                        <input type="text" id="edit_name" name="name" class="form-control" required>
                                                        <div class="error-message" id="edit_name_error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="edit_price">Price</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">AED</span>
                                                            <input type="number" id="edit_price" name="price" class="form-control" step="0.01" min="0" required>
                                                        </div>
                                                        <div class="error-message" id="edit_price_error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="edit_unit_type">Unit Type</label>
                                                        <select class="form-select" id="edit_unit_type" name="unit_type">
                                                            <option value="">Select Unit Type</option>
                                                            <option value="piece">Piece</option>
                                                            <option value="tube">Tube</option>
                                                            <option value="injection">Injection</option>
                                                            <option value="syringe">Syringe</option>
                                                            <option value="vial">Vial (ml)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" id="edit_price_per_ml_container" style="display: none;">
                                                    <div class="form-group mb-3">
                                                        <label for="edit_price_per_ml">Price per ml</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">AED</span>
                                                            <input type="number" id="edit_price_per_ml" name="price_per_ml" class="form-control" step="0.01" min="0">
                                                        </div>
                                                        <div class="error-message" id="edit_price_per_ml_error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="edit_stock_quantity">Stock Quantity</label>
                                                        <div class="input-group">
                                                            <input type="number" id="edit_stock_quantity" name="stock_quantity" class="form-control" step="0.01" min="0" required>
                                                            <span class="input-group-text" id="edit_stock_unit_display">units</span>
                                                        </div>
                                                        <div class="error-message" id="edit_stock_quantity_error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="edit_low_stock_threshold">Low Stock Threshold</label>
                                                        <div class="input-group">
                                                            <input type="number" id="edit_low_stock_threshold" name="low_stock_threshold" class="form-control" step="0.01" min="0">
                                                            <span class="input-group-text" id="edit_threshold_unit_display">units</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="edit_is_active">Status</label>
                                                        <select class="form-select" id="edit_is_active" name="is_active">
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="edit_description">Description</label>
                                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="update">Update
                                            Record</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Product Modal -->
                <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addProductForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="add_name" class="form-label">Product Name *</label>
                                                <input type="text" class="form-control" id="add_name" name="name" required>
                                                <div class="error-message" id="add_name_error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="add_price" class="form-label">Price (per unit) *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">AED</span>
                                                    <input type="number" class="form-control" id="add_price" name="price" step="0.01" min="0" required>
                                                </div>
                                                <div class="error-message" id="add_price_error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="add_unit_type" class="form-label">Unit Type *</label>
                                                <select class="form-select" id="add_unit_type" name="unit_type" required>
                                                    <option value="">Select Unit Type</option>
                                                    <option value="piece">Piece</option>
                                                    <option value="tube">Tube</option>
                                                    <option value="injection">Injection</option>
                                                    <option value="syringe">Syringe</option>
                                                    <option value="vial">Vial (ml)</option>
                                                </select>
                                                <div class="error-message" id="add_unit_type_error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" id="add_price_per_ml_container" style="display: none;">
                                            <div class="form-group mb-3">
                                                <label for="add_price_per_ml" class="form-label">Price per ml *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">AED</span>
                                                    <input type="number" class="form-control" id="add_price_per_ml" name="price_per_ml" step="0.01" min="0">
                                                </div>
                                                <div class="error-message" id="add_price_per_ml_error"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="add_stock_quantity" class="form-label">Stock Quantity *</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="add_stock_quantity" name="stock_quantity" step="0.01" min="0" required>
                                                    <span class="input-group-text" id="add_stock_unit_display">units</span>
                                                </div>
                                                <div class="error-message" id="add_stock_quantity_error"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="add_low_stock_threshold" class="form-label">Low Stock Threshold</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="add_low_stock_threshold" name="low_stock_threshold" value="5" step="0.01" min="0">
                                                    <span class="input-group-text" id="add_threshold_unit_display">units</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="add_is_active" class="form-label">Status</label>
                                                <select class="form-select" id="add_is_active" name="is_active">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="add_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="add_description" name="description" rows="3" placeholder="Enter product description..."></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveProduct">Save Product</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('page_script')

<script>
    $(document).ready(function() {
        console.log('Page loaded, starting AJAX call...');
        console.log('CSRF Token:', $('meta[name="csrf-token"]').attr('content'));
        console.log('Fetch URL:', "{{ route('products.fetch') }}");
        
        // Pagination state
        let currentPage = 1;
        let perPage = 10;
        let searchTerm = '';
        let sortBy = 'id';
        let sortOrder = 'desc';
        
        // Clear validation errors
        function clearValidationErrors() {
            $('.error-message').text('');
            $('.form-control').removeClass('is-invalid');
        }
        
        // Display validation errors
        function displayValidationErrors(errors) {
            clearValidationErrors();
            
            for (var field in errors) {
                var errorElement = $('#' + field + '_error');
                var inputElement = $('#' + field);
                
                if (errorElement.length && inputElement.length) {
                    errorElement.text(errors[field][0]);
                    inputElement.addClass('is-invalid');
                }
            }
        }

        // Initial load
        loadProducts();

        // Unit type change handlers for dynamic field visibility
        function updateFieldsForUnitType(unitType, prefix) {
            const isVial = unitType === 'vial';
            const pricePerMlContainer = $('#' + prefix + '_price_per_ml_container');
            const pricePerMlField = $('#' + prefix + '_price_per_ml');
            const stockUnitDisplay = $('#' + prefix + '_stock_unit_display');
            const thresholdUnitDisplay = $('#' + prefix + '_threshold_unit_display');

            if (isVial) {
                pricePerMlContainer.show();
                pricePerMlField.attr('required', true);
                stockUnitDisplay.text('ml');
                thresholdUnitDisplay.text('ml');
            } else {
                pricePerMlContainer.hide();
                pricePerMlField.attr('required', false).val('');
                stockUnitDisplay.text('units');
                thresholdUnitDisplay.text('units');
            }
        }

        // Add unit type change handlers
        $('#add_unit_type').on('change', function() {
            updateFieldsForUnitType($(this).val(), 'add');
        });

        $('#edit_unit_type').on('change', function() {
            updateFieldsForUnitType($(this).val(), 'edit');
        });

        // Refresh button
        $('#refreshBtn').on('click', function() {
            currentPage = 1;
            searchTerm = '';
            $('#searchInput').val('');
            loadProducts();
        });
        
        // Search functionality
        $('#searchBtn').on('click', function() {
            searchTerm = $('#searchInput').val().trim();
            currentPage = 1;
            loadProducts();
        });
        
        // Search on Enter key
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) {
                $('#searchBtn').click();
            }
        });
        
        // Per page change
        $('#perPageSelect').on('change', function() {
            perPage = parseInt($(this).val());
            currentPage = 1;
            loadProducts();
        });
        
        // Sorting functionality
        $(document).on('click', '.sortable', function() {
            var column = $(this).data('sort');
            
            // Remove active sort from all headers
            $('.sortable').removeClass('sort-asc sort-desc');
            
            // Determine sort order
            if (sortBy === column && sortOrder === 'asc') {
                sortOrder = 'desc';
                $(this).addClass('sort-desc');
            } else {
                sortOrder = 'asc';
                $(this).addClass('sort-asc');
            }
            
            sortBy = column;
            currentPage = 1;
            loadProducts();
        });
        
        // Load products function
        function loadProducts() {
            var requestData = {
                page: currentPage,
                per_page: perPage,
                search: searchTerm,
                sort_by: sortBy,
                sort_order: sortOrder,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.ajax({
                url: "{{ route('products.fetch') }}",
                type: "POST",
                dataType: "JSON",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: requestData,
                beforeSend: function() {
                    console.log('Sending AJAX request...');
                    $('#tbody').html('<tr><td colspan="7" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                },
                success: function(response) {
                    console.log('Response received:', response);
                    
                    if (response.data && response.data.length > 0) {
                        // Populate table
                        var tbody = $('#tbody');
                        tbody.empty();
                        
                        response.data.forEach(function(product) {
                            var row = '<tr>' +
                                '<td>' + product.id + '</td>' +
                                '<td>' + product.name + '</td>' +
                                '<td>' + product.price + '</td>' +
                                '<td>' + product.quantity + '</td>' +
                                '<td>' + product.category + '</td>' +
                                '<td>' + product.status + '</td>' +
                                '<td>' + product.action + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                        
                        // Show table and hide no-data
                        $('#table').show();
                        $('#no-data').hide();
                        
                        // Update pagination info
                        updatePaginationInfo(response.pagination);
                        
                        // Generate pagination controls
                        generatePagination(response.pagination);
                        
                        // Add click event handlers for edit buttons
                        $('.edit-product').on('click', function(e) {
                            e.preventDefault();
                            var id = $(this).data('id');
                            editRow(id);
                        });
                    } else {
                        $('#table').hide();
                        $('#no-data').show();
                        $('#pagination').empty();
                        $('#paginationInfo').text('Showing 0 to 0 of 0 entries');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                    console.error('Status:', status);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status Code:', xhr.status);
                    
                    $('#table').hide();
                    $('#no-data').show();
                    $('#no-data p').html('Error loading products. Status: ' + xhr.status + '. Please try again.');
                    $('#pagination').empty();
                }
            });
        }
        
        // Update pagination info
        function updatePaginationInfo(pagination) {
            var info = 'Showing ' + pagination.from + ' to ' + pagination.to + ' of ' + pagination.total + ' entries';
            $('#paginationInfo').text(info);
        }
        
        // Generate pagination controls
        function generatePagination(pagination) {
            var $pagination = $('#pagination');
            $pagination.empty();
            
            if (pagination.last_page <= 1) {
                return;
            }
            
            // Previous button
            var prevClass = pagination.current_page === 1 ? 'page-item disabled' : 'page-item';
            var prevHtml = '<li class="' + prevClass + '"><a class="page-link" href="#" data-page="' + (pagination.current_page - 1) + '">Previous</a></li>';
            $pagination.append(prevHtml);
            
            // Page numbers
            var startPage = Math.max(1, pagination.current_page - 2);
            var endPage = Math.min(pagination.last_page, pagination.current_page + 2);
            
            if (startPage > 1) {
                $pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>');
                if (startPage > 2) {
                    $pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
                }
            }
            
            for (var i = startPage; i <= endPage; i++) {
                var pageClass = i === pagination.current_page ? 'page-item active' : 'page-item';
                var pageHtml = '<li class="' + pageClass + '"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>';
                $pagination.append(pageHtml);
            }
            
            if (endPage < pagination.last_page) {
                if (endPage < pagination.last_page - 1) {
                    $pagination.append('<li class="page-item disabled"><span class="page-link">...</span></li>');
                }
                $pagination.append('<li class="page-item"><a class="page-link" href="#" data-page="' + pagination.last_page + '">' + pagination.last_page + '</a></li>');
            }
            
            // Next button
            var nextClass = pagination.current_page === pagination.last_page ? 'page-item disabled' : 'page-item';
            var nextHtml = '<li class="' + nextClass + '"><a class="page-link" href="#" data-page="' + (pagination.current_page + 1) + '">Next</a></li>';
            $pagination.append(nextHtml);
            
            // Add click handlers for pagination
            $pagination.on('click', '.page-link', function(e) {
                e.preventDefault();
                var page = $(this).data('page');
                if (page && page !== pagination.current_page && !$(this).parent().hasClass('disabled')) {
                    currentPage = page;
                    loadProducts();
                }
            });
        }
        
        // Add Product functionality
        $('#saveProduct').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            
            clearValidationErrors();
            
            // Validation
            var isValid = true;
            var errors = {};
            
            if (!$('#add_name').val().trim()) {
                errors['add_name'] = ['Product name is required'];
                isValid = false;
            }
            if (!$('#add_price').val() || $('#add_price').val() <= 0) {
                errors['add_price'] = ['Valid price is required'];
                isValid = false;
            }
            if (!$('#add_stock_quantity').val() || $('#add_stock_quantity').val() < 0) {
                errors['add_stock_quantity'] = ['Valid stock quantity is required'];
                isValid = false;
            }
            
            if (!isValid) {
                displayValidationErrors(errors);
                return;
            }
            
            // Disable button and show loading
            $btn.prop('disabled', true).text('Saving...');
            
            var formData = {
                name: $('#add_name').val().trim(),
                price: $('#add_price').val(),
                price_per_ml: $('#add_price_per_ml').val() || null,
                stock_quantity: $('#add_stock_quantity').val(),
                unit_type: $('#add_unit_type').val(),
                low_stock_threshold: $('#add_low_stock_threshold').val() || 5,
                is_active: $('#add_is_active').val(),
                description: $('#add_description').val().trim(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.ajax({
                url: "{{ route('products.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#addProductModal').modal('hide');
                        $('#addProductForm')[0].reset();
                        // alert('Product created successfully!');
                        Swal.fire({
                            title: "Good job!",
                            text: "Product created successfully!",
                            icon: "success"
                        });
                        // Refresh the table
                        currentPage = 1;
                        loadProducts();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        displayValidationErrors(errors);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while creating the product. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                complete: function() {
                    // Re-enable button
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Edit Product functionality
        function editRow(id) {
            $.ajax({
                url: "{{ route('products.edit', ':id') }}".replace(':id', id),
                type: "GET",
                data: { edit_id: id },
                success: function(response) {
                    if (response.response === "success") {
                        var product = response.post;
                        $('#edit_modal_id').val(product.id);
                        $('#edit_name').val(product.name);
                        $('#edit_price').val(product.price);
                        $('#edit_price_per_ml').val(product.price_per_ml || '');
                        $('#edit_stock_quantity').val(product.stock_quantity);
                        $('#edit_unit_type').val(product.unit_type);
                        $('#edit_low_stock_threshold').val(product.low_stock_threshold);
                        $('#edit_is_active').val(product.is_active ? '1' : '0');
                        $('#edit_description').val(product.description);

                        // Update unit type fields after setting values
                        updateFieldsForUnitType(product.unit_type, 'edit');

                        $('#editModel').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Product not found',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error loading product data. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                }
            });
        }
        
        // Update Product functionality
        $('#update').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            var id = $('#edit_modal_id').val();
            
            clearValidationErrors();
            
            // Validation
            var isValid = true;
            var errors = {};
            
            if (!$('#edit_name').val().trim()) {
                errors['edit_name'] = ['Product name is required'];
                isValid = false;
            }
            if (!$('#edit_price').val() || $('#edit_price').val() <= 0) {
                errors['edit_price'] = ['Valid price is required'];
                isValid = false;
            }
            if (!$('#edit_stock_quantity').val() || $('#edit_stock_quantity').val() < 0) {
                errors['edit_stock_quantity'] = ['Valid stock quantity is required'];
                isValid = false;
            }
            
            if (!isValid) {
                displayValidationErrors(errors);
                return;
            }
            
            // Disable button and show loading
            $btn.prop('disabled', true).text('Updating...');
            
            var formData = {
                name: $('#edit_name').val().trim(),
                price: $('#edit_price').val(),
                price_per_ml: $('#edit_price_per_ml').val() || null,
                stock_quantity: $('#edit_stock_quantity').val(),
                unit_type: $('#edit_unit_type').val(),
                low_stock_threshold: $('#edit_low_stock_threshold').val() || 5,
                is_active: $('#edit_is_active').val(),
                description: $('#edit_description').val().trim(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT'
            };
            
            $.ajax({
                url: "{{ route('products.update', ':id') }}".replace(':id', id),
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.response === "success") {
                        $('#editModel').modal('hide');
                        // alert('Product updated successfully!');
                        Swal.fire({
                            title: "Good job!",
                            text: "Product updated successfully!",
                            icon: "success"
                        });
                        // Refresh the table
                        loadProducts();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        displayValidationErrors(errors);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the product. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                complete: function() {
                    // Re-enable button
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Clear form when modals are closed
        $('#addProductModal').on('hidden.bs.modal', function() {
            $('#addProductForm')[0].reset();
            $('#add_price_per_ml_container').hide();
            $('#add_stock_unit_display').text('units');
            $('#add_threshold_unit_display').text('units');
            clearValidationErrors();
        });

        $('#editModel').on('hidden.bs.modal', function() {
            $('#edit_form')[0].reset();
            $('#edit_price_per_ml_container').hide();
            $('#edit_stock_unit_display').text('units');
            $('#edit_threshold_unit_display').text('units');
            clearValidationErrors();
        });
    })
</script>
@endsection