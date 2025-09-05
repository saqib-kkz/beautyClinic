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
    </style>
@endsection

@section('title')
    Clients
@endsection

@section('sub-title')
    Clients List
@endsection

@section('page')
    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                {!! displayAlert() !!}
                <div class="card">
                    <div class="card-header align-items-center justify-content-between d-flex py-3">
                        <h5 class="card-title">All Clients</h5>
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" class="form-control" id="searchInput" placeholder="Search clients...">
                                <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            <button class="btn btn-outline-secondary" type="button" id="refreshBtn" title="Refresh">
                                <i class="bi bi-arrow-clockwise"></i>
                            </button>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClientModal">+ Add</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table" id="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Full Name</th>
                                    <th scope="col">Contact Number</th>
                                    <th scope="col">Notes</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                        <div id="no-data" class="text-center py-4" style="display: none;">
                            <p class="text-muted">No clients found. <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addClientModal">Add First Client</button></p>
                        </div>
                    </div>
                </div>

                <!-- Add Client Modal -->
                <div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addClientForm">
                                    <div class="form-group mb-3">
                                        <label for="add_full_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="add_full_name" name="full_name" required>
                                        <div class="error-message" id="add_full_name_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="add_contact_number" class="form-label">Contact Number *</label>
                                        <input type="text" class="form-control" id="add_contact_number" name="contact_number" required>
                                        <div class="error-message" id="add_contact_number_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="add_notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="add_notes" name="notes" rows="3" placeholder="Enter client notes..."></textarea>
                                        <div class="error-message" id="add_notes_error"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveClient">Save Client</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Client Modal -->
                <div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editClientForm">
                                    <input type="hidden" id="edit_client_id" value="">
                                    <div class="form-group mb-3">
                                        <label for="edit_full_name" class="form-label">Full Name *</label>
                                        <input type="text" id="edit_full_name" name="full_name" class="form-control" required>
                                        <div class="error-message" id="edit_full_name_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_contact_number" class="form-label">Contact Number *</label>
                                        <input type="text" id="edit_contact_number" name="contact_number" class="form-control" required>
                                        <div class="error-message" id="edit_contact_number_error"></div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                                        <div class="error-message" id="edit_notes_error"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="updateClient">Update Client</button>
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
        // Load clients function
        function loadClients() {
            var searchTerm = $('#searchInput').val().trim();
            
            $.ajax({
                url: "{{ route('clients.fetch') }}",
                type: "POST",
                dataType: "JSON",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    search: searchTerm,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#tbody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></td></tr>');
                },
                success: function(response) {
                    if (response.data && response.data.length > 0) {
                        var tbody = $('#tbody');
                        tbody.empty();
                        
                        response.data.forEach(function(client) {
                            var row = '<tr>' +
                                '<td>' + client.id + '</td>' +
                                '<td>' + client.full_name + '</td>' +
                                '<td>' + client.contact_number + '</td>' +
                                '<td>' + (client.notes || '') + '</td>' +
                                '<td>' + client.action + '</td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                        
                        $('#table').show();
                        $('#no-data').hide();
                    } else {
                        $('#table').hide();
                        $('#no-data').show();
                    }
                },
                error: function(xhr) {
                    $('#table').hide();
                    $('#no-data').show();
                    $('#no-data p').html('Error loading clients. Please try again.');
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
        
        // Initial load
        loadClients();
        
        // Event delegation for edit buttons
        $(document).on('click', '.edit-client', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            editClient(id);
        });
        
        // Refresh button
        $('#refreshBtn').on('click', function() {
            loadClients();
        });
        
        // Search functionality
        $('#searchBtn').on('click', function() {
            loadClients();
        });
        
        // Search on Enter key
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) {
                $('#searchBtn').click();
            }
        });
        
        // Add Client functionality
        $('#saveClient').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            
            clearValidationErrors();
            
            // Validation
            var isValid = true;
            var errors = {};
            
            if (!$('#add_full_name').val().trim()) {
                errors['add_full_name'] = ['Full name is required'];
                isValid = false;
            }
            
            if (!$('#add_contact_number').val().trim()) {
                errors['add_contact_number'] = ['Contact number is required'];
                isValid = false;
            }
            
            if (!isValid) {
                displayValidationErrors(errors);
                return;
            }
            
            $btn.prop('disabled', true).text('Saving...');
            
            var formData = {
                full_name: $('#add_full_name').val().trim(),
                contact_number: $('#add_contact_number').val().trim(),
                notes: $('#add_notes').val().trim(),
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            
            $.ajax({
                url: "{{ route('clients.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#addClientModal').modal('hide');
                        $('#addClientForm')[0].reset();
                        clearValidationErrors();
                        alert('Client created successfully!');
                        loadClients();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        displayValidationErrors(errors);
                    } else {
                        alert('An error occurred while creating the client. Please try again.');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Edit Client functionality
        function editClient(id) {
            $.ajax({
                url: "{{ route('clients.edit', ':id') }}".replace(':id', id),
                type: "GET",
                data: { edit_id: id },
                success: function(response) {
                    if (response.response === "success") {
                        var client = response.post;
                        $('#edit_client_id').val(client.id);
                        $('#edit_full_name').val(client.full_name);
                        $('#edit_contact_number').val(client.contact_number);
                        $('#edit_notes').val(client.notes);
                        clearValidationErrors();
                        $('#editClientModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 404) {
                        alert('Client not found');
                    } else {
                        alert('Error loading client data. Please try again.');
                    }
                }
            });
        }
        
        // Update Client functionality
        $('#updateClient').on('click', function() {
            var $btn = $(this);
            var originalText = $btn.text();
            var id = $('#edit_client_id').val();
            
            clearValidationErrors();
            
            // Validation
            var isValid = true;
            var errors = {};
            
            if (!$('#edit_full_name').val().trim()) {
                errors['edit_full_name'] = ['Full name is required'];
                isValid = false;
            }
            
            if (!$('#edit_contact_number').val().trim()) {
                errors['edit_contact_number'] = ['Contact number is required'];
                isValid = false;
            }
            
            if (!isValid) {
                displayValidationErrors(errors);
                return;
            }
            
            $btn.prop('disabled', true).text('Updating...');
            
            var formData = {
                full_name: $('#edit_full_name').val().trim(),
                contact_number: $('#edit_contact_number').val().trim(),
                notes: $('#edit_notes').val().trim(),
                _token: $('meta[name="csrf-token"]').attr('content'),
                _method: 'PUT'
            };
            
            $.ajax({
                url: "{{ route('clients.update', ':id') }}".replace(':id', id),
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.response === "success") {
                        $('#editClientModal').modal('hide');
                        alert('Client updated successfully!');
                        loadClients();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        displayValidationErrors(errors);
                    } else {
                        alert('An error occurred while updating the client. Please try again.');
                    }
                },
                complete: function() {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
        
        // Clear form when modals are closed
        $('#addClientModal').on('hidden.bs.modal', function() {
            $('#addClientForm')[0].reset();
            clearValidationErrors();
        });
        
        $('#editClientModal').on('hidden.bs.modal', function() {
            $('#editClientForm')[0].reset();
            clearValidationErrors();
        });
    });
</script>
@endsection