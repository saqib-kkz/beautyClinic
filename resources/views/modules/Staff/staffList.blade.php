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
        .modal-lg {
            max-width: 900px;
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
        .staff-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('title')
    Staff Management
@endsection

@section('sub-title')
    Manage Staff Members
@endsection

@section('page')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <!-- Staff Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 id="totalStaff">0</h4>
                                        <span>Total Staff</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 id="activeStaff">0</h4>
                                        <span>Active Staff</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 id="inactiveStaff">0</h4>
                                        <span>Inactive Staff</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-pause-circle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 id="totalTreatments">0</h4>
                                        <span>Total Treatments</span>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clipboard2-pulse" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title">Staff Members</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                                <i class="bi bi-plus-circle"></i> Add New Staff
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search staff...">
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

                        <!-- Staff Table -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="staffTable">
                                <thead>
                                    <tr>
                                        <th class="sortable" data-column="name">
                                            Name <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="email">
                                            Email <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="role">
                                            Role <span class="sort-icon">⇅</span>
                                        </th>
                                        <th class="sortable" data-column="is_active">
                                            Status <span class="sort-icon">⇅</span>
                                        </th>
                                        <th>Treatments</th>
                                        <th class="sortable" data-column="created_at">
                                            Joined <span class="sort-icon">⇅</span>
                                        </th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="staffTableBody">
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
                        <nav aria-label="Staff pagination">
                            <ul class="pagination justify-content-center" id="pagination">
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Staff Modal -->
    <div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStaffModalLabel">Add New Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="add_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="add_name" name="name" required>
                                    <div class="error-message" id="add_name_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="add_email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="add_email" name="email" required>
                                    <div class="error-message" id="add_email_error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="add_password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="add_password" name="password" required>
                                    <div class="error-message" id="add_password_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="add_password_confirmation" class="form-label">Confirm Password *</label>
                                    <input type="password" class="form-control" id="add_password_confirmation" name="password_confirmation" required>
                                    <div class="error-message" id="add_password_confirmation_error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="add_role" class="form-label">Role *</label>
                                    <select class="form-select" id="add_role" name="role" required>
                                        <option value="staff" selected>Staff</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <div class="error-message" id="add_role_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="add_is_active" name="is_active" checked>
                                        <label class="form-check-label" for="add_is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveStaff">Save Staff Member</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel">Edit Staff Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStaffForm">
                        <input type="hidden" id="edit_staff_id" value="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" required>
                                    <div class="error-message" id="edit_name_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                    <div class="error-message" id="edit_email_error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                                    <input type="password" class="form-control" id="edit_password" name="password">
                                    <div class="error-message" id="edit_password_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                                    <div class="error-message" id="edit_password_confirmation_error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_role" class="form-label">Role *</label>
                                    <select class="form-select" id="edit_role" name="role" required>
                                        <option value="staff">Staff</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <div class="error-message" id="edit_role_error"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                                        <label class="form-check-label" for="edit_is_active">
                                            Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="updateStaff">Update Staff Member</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_script')
    <script>
        let currentPage = 1;
        let currentSort = { column: 'created_at', order: 'desc' };
        let currentSearch = '';
        let currentPerPage = 10;
        let isLoading = false;

        // Clear validation errors - moved to global scope
        function clearValidationErrors() {
            $('.error-message').text('');
            $('.form-control').removeClass('is-invalid');
        }
        
        // Display validation errors - moved to global scope
        function displayValidationErrors(errors) {
            clearValidationErrors();
            
            console.log('Displaying validation errors:', errors);
            console.log('Available error elements:', $('.error-message').map(function() { return this.id; }).get());
            console.log('Available input elements:', $('.form-control').map(function() { return this.id; }).get());
            
            for (var field in errors) {
                console.log('Processing field:', field);
                console.log('Error message:', errors[field][0]);
                
                // Try different combinations to find the error elements
                // Check edit elements first since they're more specific
                var possibleErrorSelectors = [
                    '#edit_' + field + '_error',
                    '#add_' + field + '_error', 
                    '#' + field + '_error'
                ];
                
                var possibleInputSelectors = [
                    '#edit_' + field,
                    '#add_' + field,
                    '#' + field
                ];
                
                var errorElement = null;
                var inputElement = null;
                
                // Find the error element
                for (var i = 0; i < possibleErrorSelectors.length; i++) {
                    var el = $(possibleErrorSelectors[i]);
                    if (el.length > 0) {
                        errorElement = el;
                        console.log('Found error element with selector:', possibleErrorSelectors[i]);
                        break;
                    }
                }
                
                // Find the input element
                for (var j = 0; j < possibleInputSelectors.length; j++) {
                    var el = $(possibleInputSelectors[j]);
                    if (el.length > 0) {
                        inputElement = el;
                        console.log('Found input element with selector:', possibleInputSelectors[j]);
                        break;
                    }
                }
                
                if (errorElement && inputElement) {
                    errorElement.text(errors[field][0]);
                    inputElement.addClass('is-invalid');
                    errorElement.show(); // Make sure it's visible
                    console.log('Successfully displayed error for field:', field);
                } else {
                    console.log('Could not find elements for field:', field);
                    console.log('Error element found:', errorElement ? true : false);
                    console.log('Input element found:', inputElement ? true : false);
                }
            }
        }

        $(document).ready(function() {
            loadStaff();
            
            // Check if we need to auto-open edit modal from view page
            checkAutoEdit();

            // Search functionality
            $('#searchInput').on('input', debounce(function() {
                currentSearch = $(this).val();
                currentPage = 1;
                loadStaff();
            }, 500));
            
            // Search button
            $('#searchBtn').on('click', function() {
                currentSearch = $('#searchInput').val();
                currentPage = 1;
                loadStaff();
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
                loadStaff();
            });

            // Per page change
            $('#perPageSelect').on('change', function() {
                currentPerPage = parseInt($(this).val());
                currentPage = 1;
                loadStaff();
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
                loadStaff();
                
                // Update sort icons
                $('.sort-icon').html('⇅');
                $(this).find('.sort-icon').html(currentSort.order === 'asc' ? '↑' : '↓');
            });

            // Add Staff functionality
            $('#saveStaff').on('click', function() {
                var $btn = $(this);
                var originalText = $btn.text();
                
                clearValidationErrors();
                $btn.prop('disabled', true).text('Saving...');
                
                var formData = {
                    name: $('#add_name').val().trim(),
                    email: $('#add_email').val().trim(),
                    password: $('#add_password').val(),
                    password_confirmation: $('#add_password_confirmation').val(),
                    role: $('#add_role').val(),
                    is_active: $('#add_is_active').is(':checked') ? 1 : 0,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                
                $.ajax({
                    url: "{{ route('staff.store') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#addStaffModal').modal('hide');
                            $('#addStaffForm')[0].reset();
                            clearValidationErrors();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Staff member created successfully!',
                                confirmButtonColor: '#28a745'
                            });
                            loadStaff();
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
                                text: 'An error occurred while creating the staff member. Please try again.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Functions are now called directly via onclick handlers in HTML

            // Update Staff functionality
            $('#updateStaff').on('click', function() {
                var $btn = $(this);
                var originalText = $btn.text();
                var id = $('#edit_staff_id').val();
                
                clearValidationErrors();
                $btn.prop('disabled', true).text('Updating...');
                
                var formData = {
                    name: $('#edit_name').val().trim(),
                    email: $('#edit_email').val().trim(),
                    role: $('#edit_role').val(),
                    is_active: $('#edit_is_active').is(':checked') ? 1 : 0,
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'PUT'
                };

                if ($('#edit_password').val()) {
                    formData.password = $('#edit_password').val();
                    formData.password_confirmation = $('#edit_password_confirmation').val();
                }
                
                console.log('Update formData:', formData);
                console.log('Update URL:', "{{ route('staff.update', ':id') }}".replace(':id', id));
                console.log('Name field value:', $('#edit_name').val());
                console.log('Email field value:', $('#edit_email').val());
                console.log('Staff ID:', id);
                
                $.ajax({
                    url: "{{ route('staff.update', ':id') }}".replace(':id', id),
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            $('#editStaffModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Staff member updated successfully!',
                                confirmButtonColor: '#28a745'
                            });
                            loadStaff();
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
                        console.log('Update error:', xhr);
                        console.log('Response:', xhr.responseJSON);
                        
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            console.log('Validation errors:', errors);
                            displayValidationErrors(errors);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while updating the staff member. Please try again.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text(originalText);
                    }
                });
            });

            // Clear form when modals are closed
            $('#addStaffModal').on('hidden.bs.modal', function() {
                $('#addStaffForm')[0].reset();
                clearValidationErrors();
            });
            
            $('#editStaffModal').on('hidden.bs.modal', function() {
                $('#editStaffForm')[0].reset();
                clearValidationErrors();
            });
        });

        function loadStaff() {
            if (isLoading) return;
            
            isLoading = true;
            $('#staffTable').addClass('loading');
            
            // Show loader in table body
            $('#staffTableBody').html(`
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: '{{ route("staff.index") }}',
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
                    renderStaffTable(response.data);
                    renderPagination(response);
                    updateStats(response.data);
                },
                error: function(xhr) {
                    console.error('Error loading staff:', xhr);
                    showErrorMessage('Error loading staff. Please try again.');
                },
                complete: function() {
                    isLoading = false;
                    $('#staffTable').removeClass('loading');
                }
            });
        }

        function renderStaffTable(staff) {
            const tbody = $('#staffTableBody');
            tbody.empty();

            if (staff.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No staff members found
                        </td>
                    </tr>
                `);
                return;
            }

            staff.forEach(member => {
                tbody.append(`
                    <tr>
                        <td>
                            <strong>${member.name}</strong>
                        </td>
                        <td>${member.email}</td>
                        <td>
                            <span class="badge ${member.role.toLowerCase() === 'admin' ? 'bg-danger' : (member.role.toLowerCase() === 'manager' ? 'bg-info' : 'bg-secondary')}">
                                ${member.role}
                            </span>
                        </td>
                        <td>${member.status}</td>
                        <td>
                            <span class="badge bg-primary">${member.treatments_count}</span>
                        </td>
                        <td>${member.created_at}</td>
                        <td>${member.action}</td>
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

        function updateStats(staff) {
            const totalStaff = staff.length;
            const activeStaff = staff.filter(s => s.is_active).length;
            const inactiveStaff = totalStaff - activeStaff;
            const totalTreatments = staff.reduce((sum, s) => sum + s.treatments_count, 0);

            $('#totalStaff').text(totalStaff);
            $('#activeStaff').text(activeStaff);
            $('#inactiveStaff').text(inactiveStaff);
            $('#totalTreatments').text(totalTreatments);
        }

        function changePage(page) {
            currentPage = page;
            loadStaff();
        }

        function editStaff(id) {
            console.log('editStaff function called with ID:', id);
            
            $.ajax({
                url: "{{ route('staff.edit', ':id') }}".replace(':id', id),
                type: "GET",
                success: function(response) {
                    console.log('Edit staff response:', response);
                    if (response.success) {
                        var staff = response.staff;
                        console.log('Staff data received:', staff);
                        
                        $('#edit_staff_id').val(staff.id);
                        $('#edit_name').val(staff.name);
                        $('#edit_email').val(staff.email);
                        $('#edit_role').val(staff.role || 'staff');
                        $('#edit_is_active').prop('checked', staff.is_active == 1);
                        $('#edit_password').val('');
                        $('#edit_password_confirmation').val('');
                        
                        // Verify the fields were set correctly
                        console.log('After setting values:');
                        console.log('edit_staff_id:', $('#edit_staff_id').val());
                        console.log('edit_name:', $('#edit_name').val());
                        console.log('edit_email:', $('#edit_email').val());
                        
                        clearValidationErrors();
                        $('#editStaffModal').modal('show');
                        console.log('Edit modal should now be visible');
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
                    console.error('Error in editStaff AJAX:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error loading staff data. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }

        function toggleStaffStatus(id) {
            console.log('toggleStaffStatus function called with ID:', id);
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will change the staff member\'s status',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('staff.toggleStatus', ':id') }}".replace(':id', id),
                        type: "POST",
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
                                loadStaff();
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error updating staff status. Please try again.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        }

        function deleteStaff(id) {
            console.log('deleteStaff function called with ID:', id);
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('staff.destroy', ':id') }}".replace(':id', id),
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    confirmButtonColor: '#28a745'
                                });
                                loadStaff();
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error deleting staff member. Please try again.',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
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

        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        }

        function checkAutoEdit() {
            // Check if we have edit data in sessionStorage (from view page)
            const editData = sessionStorage.getItem('editStaffData');
            const urlHash = window.location.hash;
            
            if (editData || urlHash.startsWith('#edit-')) {
                try {
                    let staff;
                    let staffId;
                    
                    if (editData) {
                        staff = JSON.parse(editData);
                        staffId = staff.id;
                        // Clear the session storage
                        sessionStorage.removeItem('editStaffData');
                    } else if (urlHash.startsWith('#edit-')) {
                        staffId = urlHash.replace('#edit-', '');
                        console.log('Extracted staff ID from URL hash:', staffId);
                    }
                    
                    if (staffId) {
                        // If we have staff data, use it, otherwise fetch it
                        if (staff) {
                            openEditModal(staff);
                        } else {
                            // Fetch staff data from server
                            $.ajax({
                                url: "{{ route('staff.edit', ':id') }}".replace(':id', staffId),
                                type: "GET",
                                success: function(response) {
                                    if (response.success) {
                                        openEditModal(response.staff);
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Error fetching staff data:', xhr);
                                }
                            });
                        }
                        
                        // Clean up URL hash
                        if (urlHash.startsWith('#edit-')) {
                            history.replaceState(null, null, window.location.pathname);
                        }
                    }
                } catch (e) {
                    console.error('Error parsing edit data:', e);
                    sessionStorage.removeItem('editStaffData');
                }
            }
        }
        
        // Ensure functions are available globally
        window.editStaff = editStaff;
        window.toggleStaffStatus = toggleStaffStatus;
        window.deleteStaff = deleteStaff;
        window.changePage = changePage;
        
        function openEditModal(staff) {
            // Wait a bit for the page to load, then open edit modal
            setTimeout(() => {
                $('#edit_staff_id').val(staff.id);
                $('#edit_name').val(staff.name);
                $('#edit_email').val(staff.email);
                $('#edit_role').val(staff.role || 'staff');
                $('#edit_is_active').prop('checked', staff.is_active == 1);
                $('#edit_password').val('');
                $('#edit_password_confirmation').val('');
                clearValidationErrors();
                $('#editStaffModal').modal('show');
            }, 1000);
        }
    </script>
@endsection