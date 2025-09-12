@extends('layouts.main')

@section('page_style')
    <style>
        .card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .profile-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #dee2e6;
            border-radius: 50%;
            margin-bottom: 1rem;
        }
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #007bff;
            background-color: rgba(0,123,255,0.05);
        }
        .upload-area.dragover {
            border-color: #007bff;
            background-color: rgba(0,123,255,0.1);
        }
    </style>
@endsection

@section('title')
    Edit Profile
@endsection

@section('sub-title')
    Update Profile
@endsection

@section('page')
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Edit Profile</h5>
                            <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Profile
                            </a>
                        </div>

                        <form id="profileForm" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Profile Picture Upload Section -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Current Profile Picture</label>
                                    <div class="text-center">
                                        <img id="profilePreview" src="{{ getUserProfilePic($user->profile_pic) }}" alt="Profile Picture" class="profile-preview">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Upload New Profile Picture</label>
                                    <div class="upload-area" onclick="document.getElementById('profileInput').click()">
                                        <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                                        <p class="mt-2 mb-1">Click to upload or drag and drop</p>
                                        <small class="text-muted">PNG, JPG, GIF up to 2MB</small>
                                    </div>
                                    <input type="file" id="profileInput" name="profile_pic" accept="image/*" style="display: none;">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <hr>

                            <!-- Personal Information -->
                            <h6>Personal Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ $user->name }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ $user->email }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Change Section -->
                            <h6>Change Password</h6>
                            <p class="text-muted small">Leave password fields blank if you don't want to change your password.</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password" minlength="8">
                                        <small class="text-muted">Minimum 8 characters</small>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-check-circle"></i> Save Changes
                                </button>
                                <a href="{{ route('profile.index') }}" class="btn btn-secondary">
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
        $(document).ready(function() {
            // Profile picture upload functionality
            $('#profileInput').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#profilePreview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Drag and drop functionality
            const uploadArea = $('.upload-area');
            
            uploadArea.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            uploadArea.on('dragleave dragend', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            uploadArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    $('#profileInput')[0].files = files;
                    $('#profileInput').trigger('change');
                }
            });

            // Form submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                submitForm();
            });
        });

        function submitForm() {
            const $submitBtn = $('#submitBtn');
            const $form = $('#profileForm');
            
            // Disable submit button
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            // Create FormData object
            const formData = new FormData($form[0]);

            $.ajax({
                url: '{{ route("profile.update") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#28a745',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
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
                        const message = xhr.responseJSON?.message || 'An error occurred while updating the profile.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Save Changes');
                }
            });
        }
    </script>
@endsection