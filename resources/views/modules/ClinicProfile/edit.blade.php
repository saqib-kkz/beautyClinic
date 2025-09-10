@extends('layouts.main')

@section('page_style')
    <style>
        .card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .logo-preview {
            width: 150px;
            height: 150px;
            object-fit: contain;
            border: 2px solid #dee2e6;
            border-radius: 8px;
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
    Clinic Profile
@endsection

@section('sub-title')
    Edit Profile
@endsection

@section('page')
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Edit Clinic Profile</h5>
                            <a href="{{ route('clinic-profile.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Profile
                            </a>
                        </div>

                        <form id="profileForm" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Logo Upload Section -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Current Logo</label>
                                    <div class="text-center">
                                        <img id="logoPreview" src="{{ $profile->logo_url }}" alt="Current Logo" class="logo-preview">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Upload New Logo</label>
                                    <div class="upload-area" onclick="document.getElementById('logoInput').click()">
                                        <i class="bi bi-cloud-upload" style="font-size: 3rem; color: #6c757d;"></i>
                                        <p class="mt-2 mb-1">Click to upload or drag and drop</p>
                                        <small class="text-muted">PNG, JPG, GIF up to 2MB</small>
                                    </div>
                                    <input type="file" id="logoInput" name="logo" accept="image/*" style="display: none;">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <hr>

                            <!-- Basic Information -->
                            <h6>Basic Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="clinic_name" class="form-label">Clinic Name *</label>
                                        <input type="text" class="form-control" id="clinic_name" name="clinic_name" 
                                               value="{{ $profile->clinic_name }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" 
                                               value="{{ $profile->phone }}" placeholder="+971 XX XXX XXXX">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ $profile->email }}" placeholder="info@clinic.com">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website" class="form-label">Website</label>
                                        <input type="url" class="form-control" id="website" name="website" 
                                               value="{{ $profile->website }}" placeholder="https://www.clinic.com">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address and Description -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" 
                                                  placeholder="Full clinic address">{{ $profile->address }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3" 
                                                  placeholder="Brief description of your clinic">{{ $profile->description }}</textarea>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Legal Information -->
                            <h6>Legal Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_number" class="form-label">Tax Number</label>
                                        <input type="text" class="form-control" id="tax_number" name="tax_number" 
                                               value="{{ $profile->tax_number }}" placeholder="Tax registration number">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="license_number" class="form-label">License Number</label>
                                        <input type="text" class="form-control" id="license_number" name="license_number" 
                                               value="{{ $profile->license_number }}" placeholder="Business license number">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-check-circle"></i> Save Changes
                                </button>
                                <a href="{{ route('clinic-profile.index') }}" class="btn btn-secondary">
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
            // Logo upload functionality
            $('#logoInput').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#logoPreview').attr('src', e.target.result);
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
                    $('#logoInput')[0].files = files;
                    $('#logoInput').trigger('change');
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
                url: '{{ route("clinic-profile.update") }}',
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
                        const message = xhr.responseJSON?.message || 'An error occurred while saving the profile.';
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