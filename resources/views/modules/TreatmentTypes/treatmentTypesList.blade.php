@extends('layouts.main')

@section('page_style')
    <style>
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
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
        .usage-count {
            font-weight: bold;
            color: #007bff;
        }
    </style>
@endsection

@section('title')
    Treatment Types
@endsection

@section('sub-title')
    Manage Treatment Types
@endsection

@section('page')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                {!! displayAlert() !!}
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">All Treatment Types</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTreatmentTypeModal">
                                <i class="bi bi-plus-circle"></i> Add New Type
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search treatment types...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select id="perPageSelect" class="form-select">
                                    <option value="10">10 per page</option>
                                    <option value="25">25 per page</option>
                                    <option value="50">50 per page</option>
                                    <option value="100">100 per page</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-outline-secondary" type="button" id="refreshBtn" title="Refresh">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="treatmentTypesTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-column="name">
                                            Name <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="description">
                                            Description <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="price">
                                            Price <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="usage_count">
                                            Usage Count <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="is_active">
                                            Status <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="created_at">
                                            Created <span class="sort-icon">⇅</span>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="treatmentTypesTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Treatment Types pagination">
                            <ul class="pagination justify-content-center" id="pagination">
                            </ul>
                        </nav>
                    </div>
                </div>

                <!-- Add Treatment Type Modal -->
                <div class="modal fade" id="addTreatmentTypeModal" tabindex="-1" aria-labelledby="addTreatmentTypeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addTreatmentTypeModalLabel">Add New Treatment Type</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addTreatmentTypeForm">
                                    <div class="form-group mb-3">
                                        <label for="add_name" class="form-label">Name *</label>
                                        <input type="text" class="form-control" id="add_name" name="name" required>
                                        <div class="error-message" id="add_name_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="add_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="add_description" name="description" rows="3" placeholder="Enter treatment type description..."></textarea>
                                        <div class="error-message" id="add_description_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="add_price" class="form-label">Price</label>
                                        <input type="number" class="form-control" id="add_price" name="price" step="0.01" min="0" max="99999.99" placeholder="0.00">
                                        <div class="error-message" id="add_price_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" checked>
                                            <label class="form-check-label" for="add_is_active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveTreatmentType">Save Treatment Type</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Treatment Type Modal -->
                <div class="modal fade" id="editTreatmentTypeModal" tabindex="-1" aria-labelledby="editTreatmentTypeModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editTreatmentTypeModalLabel">Edit Treatment Type</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editTreatmentTypeForm">
                                    <input type="hidden" id="edit_treatment_type_id" value="">
                                    <div class="form-group mb-3">
                                        <label for="edit_name" class="form-label">Name *</label>
                                        <input type="text" id="edit_name" name="name" class="form-control" required>
                                        <div class="error-message" id="edit_name_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                        <div class="error-message" id="edit_description_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_price" class="form-label">Price</label>
                                        <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" max="99999.99" placeholder="0.00">
                                        <div class="error-message" id="edit_price_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                            <label class="form-check-label" for="edit_is_active">
                                                Active
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="updateTreatmentType">Update Treatment Type</button>
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
        let currentPage = 1;
        let currentSort = { column: 'created_at', order: 'desc' };
        let currentSearch = '';
        let currentPerPage = 10;
        let isLoading = false;

        $(document).ready(function() {
            loadTreatmentTypes();

            // Search functionality
            $('#searchInput').on('input', debounce(function() {
                currentSearch = $(this).val();
                currentPage = 1;
                loadTreatmentTypes();
            }, 500));
            
            // Search button
            $('#searchBtn').on('click', function() {
                currentSearch = $('#searchInput').val();
                currentPage = 1;
                loadTreatmentTypes();
            });
            
            // Search on Enter key
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });
            
            // Refresh button
            $('#refreshBtn').on('click', function() {
                currentSearch = '';
                $('#searchInput').val('');
                currentPage = 1;
                loadTreatmentTypes();
            });

            // Per page change
            $('#perPageSelect').on('change', function() {
                currentPerPage = parseInt($(this).val());
                currentPage = 1;
                loadTreatmentTypes();
            });

            // Sorting
            $('.sortable').on('click', function() {
                const column = $(this).data('column');
                
                if (currentSort.column === column) {
                    currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.order = 'asc';
                }
                
                currentPage = 1;
                loadTreatmentTypes();
                
                // Update sort icons
                $('.sort-icon').html('⇅');
                $(this).find('.sort-icon').html(currentSort.order === 'asc' ? '↑' : '↓');
            });

            // Event delegation for edit buttons
            $(document).on('click', '.edit-treatment-type', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                editTreatmentType(id);
            });

            // Event delegation for toggle status buttons
            $(document).on('click', '.toggle-status', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                toggleStatus(id);
            });

            // Add Treatment Type
            $('#saveTreatmentType').on('click', function() {
                saveTreatmentType();
            });

            // Update Treatment Type
            $('#updateTreatmentType').on('click', function() {
                updateTreatmentType();
            });

            // Clear forms when modals are closed
            $('#addTreatmentTypeModal').on('hidden.bs.modal', function() {
                $('#addTreatmentTypeForm')[0].reset();
                clearValidationErrors();
            });

            $('#editTreatmentTypeModal').on('hidden.bs.modal', function() {
                $('#editTreatmentTypeForm')[0].reset();
                clearValidationErrors();
            });
        });

        function loadTreatmentTypes() {
            if (isLoading) return;
            
            isLoading = true;
            $('#treatmentTypesTable').addClass('loading');
            
            // Show loader in table body
            $('#treatmentTypesTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: '{{ route("treatment-types.index") }}',
                method: 'GET',
                data: {
                    page: currentPage,
                    per_page: currentPerPage,
                    search: currentSearch,
                    sort_by: currentSort.column,
                    sort_order: currentSort.order
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    renderTreatmentTypesTable(response.data);
                    renderPagination(response);
                },
                error: function(xhr) {
                    console.error('Error loading treatment types:', xhr);
                    showErrorMessage('Error loading treatment types. Please try again.');
                },
                complete: function() {
                    isLoading = false;
                    $('#treatmentTypesTable').removeClass('loading');
                }
            });
        }

        function renderTreatmentTypesTable(treatmentTypes) {
            const tbody = $('#treatmentTypesTableBody');
            tbody.empty();

            if (treatmentTypes.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No treatment types found
                        </td>
                    </tr>
                `);
                return;
            }

            treatmentTypes.forEach(type => {
                tbody.append(`
                    <tr>
                        <td><strong>${type.name}</strong></td>
                        <td>${type.description}</td>
                        <td><strong class="text-success">${type.price}</strong></td>
                        <td><span class="usage-count">${type.usage_count}</span></td>
                        <td>${type.status}</td>
                        <td>${type.created_at}</td>
                        <td>${type.action}</td>
                    </tr>
                `);
            });
        }

        function renderPagination(response) {
            const pagination = $('#pagination');
            pagination.empty();

            if (response.last_page <= 1) return;

            // Previous button
            if (response.current_page > 1) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(${response.current_page - 1})">&laquo;</a>
                    </li>
                `);
            }

            // Page numbers
            const startPage = Math.max(1, response.current_page - 2);
            const endPage = Math.min(response.last_page, response.current_page + 2);

            for (let i = startPage; i <= endPage; i++) {
                const active = i === response.current_page ? 'active' : '';
                pagination.append(`
                    <li class="page-item ${active}">
                        <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                    </li>
                `);
            }

            // Next button
            if (response.current_page < response.last_page) {
                pagination.append(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="changePage(${response.current_page + 1})">&raquo;</a>
                    </li>
                `);
            }
        }

        function changePage(page) {
            currentPage = page;
            loadTreatmentTypes();
        }

        function saveTreatmentType() {
            const $btn = $('#saveTreatmentType');
            const originalText = $btn.text();
            
            clearValidationErrors();
            
            // Validation
            var isValid = true;
            var errors = {};
            
            if (!$('#add_name').val().trim()) {
                errors['add_name'] = ['Name is required'];
                isValid = false;
            }
            
            if (!isValid) {
                displayValidationErrors(errors);
                return;
            }
            
            $btn.prop('disabled', true).text('Saving...');
            
            var formData = {
                name: $('#add_name').val().trim(),
                description: $('#add_description').val().trim(),
                price: $('#add_price').val() || null,
                is_active: $('#add_is_active').is(':checked') ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.ajax({
                url: '{{ route("treatment-types.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#addTreatmentTypeModal').modal('hide');
                        $('#addTreatmentTypeForm')[0].reset();
                        clearValidationErrors();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#28a745'
                        });
                        loadTreatmentTypes();
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
                            text: 'An error occurred while creating the treatment type. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        }

        function editTreatmentType(id) {
            $.ajax({
                url: '{{ route("treatment-types.edit", ":id") }}'.replace(':id', id),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#edit_treatment_type_id').val(data.id);
                        $('#edit_name').val(data.name);
                        $('#edit_description').val(data.description);
                        $('#edit_price').val(data.price);
                        $('#edit_is_active').prop('checked', data.is_active);
                        clearValidationErrors();
                        $('#editTreatmentTypeModal').modal('show');
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error loading treatment type data. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }

        function updateTreatmentType() {
            var $btn = $('#updateTreatmentType');
            var originalText = $btn.text();
            var id = $('#edit_treatment_type_id').val();
            
            clearValidationErrors();
            
            // Validation
            var isValid = true;
            var errors = {};
            
            if (!$('#edit_name').val().trim()) {
                errors['edit_name'] = ['Name is required'];
                isValid = false;
            }
            
            if (!isValid) {
                displayValidationErrors(errors);
                return;
            }
            
            $btn.prop('disabled', true).text('Updating...');
            
            var formData = {
                name: $('#edit_name').val().trim(),
                description: $('#edit_description').val().trim(),
                price: $('#edit_price').val() || null,
                is_active: $('#edit_is_active').is(':checked') ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT'
            };
            
            $.ajax({
                url: '{{ route("treatment-types.update", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#editTreatmentTypeModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#28a745'
                        });
                        loadTreatmentTypes();
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
                            text: 'An error occurred while updating the treatment type. Please try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        }

        function toggleStatus(id) {
            Swal.fire({
                title: 'Change Status',
                text: 'Are you sure you want to change the status of this treatment type?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("treatment-types.toggleStatus", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message,
                                    confirmButtonColor: '#28a745'
                                });
                                loadTreatmentTypes();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating the status.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        }

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

        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
@endsection