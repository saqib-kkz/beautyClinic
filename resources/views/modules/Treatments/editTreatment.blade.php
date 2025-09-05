@extends('layouts.main')

@section('page_style')
    <style>
        .card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .info-alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('title')
    Treatments
@endsection

@section('sub-title')
    Edit Treatment
@endsection

@section('page')
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Edit Treatment Information</h5>

                        <div class="info-alert">
                            <strong>Note:</strong> Product usage cannot be modified after treatment creation to maintain stock accuracy. 
                            To change products, please create a new treatment or contact administrator.
                        </div>

                        <form id="editTreatmentForm">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Client Selection -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="client_id" class="form-label">Client *</label>
                                        <select class="form-select" id="client_id" name="client_id" required>
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}" 
                                                        {{ $client->id == $treatment->client_id ? 'selected' : '' }}>
                                                    {{ $client->full_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <!-- Treatment Date -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="treatment_date" class="form-label">Treatment Date *</label>
                                        <input type="date" class="form-control" id="treatment_date" name="treatment_date" 
                                               value="{{ $treatment->treatment_date->format('Y-m-d') }}" required>
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
                                               value="{{ $treatment->therapist_name }}" required>
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>

                                <!-- Treatment Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="treatment_name" class="form-label">Treatment Name *</label>
                                        <input type="text" class="form-control" id="treatment_name" name="treatment_name" 
                                               value="{{ $treatment->treatment_name }}" required>
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
                                                  rows="3">{{ $treatment->treatment_reason }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Notes -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" 
                                                  rows="3">{{ $treatment->notes }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Used (Read Only Display) -->
                            @if($treatment->treatmentProducts->count() > 0)
                                <div class="form-group">
                                    <label class="form-label">Products Used in Treatment</label>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity Used</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($treatment->treatmentProducts as $tp)
                                                    <tr>
                                                        <td>{{ $tp->product->name }}</td>
                                                        <td>{{ $tp->quantity_used }} {{ $tp->product->unit_type }}</td>
                                                        <td>AED {{ number_format($tp->unit_price, 2) }}</td>
                                                        <td>AED {{ number_format($tp->total_price, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-success" id="updateBtn">
                                    <i class="bi bi-check-circle"></i> Update Treatment
                                </button>
                                <a href="{{ route('treatments.show', $treatment) }}" class="btn btn-secondary">
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
            // Form submission
            $('#editTreatmentForm').on('submit', function(e) {
                e.preventDefault();
                updateTreatment();
            });
        });

        function updateTreatment() {
            const $updateBtn = $('#updateBtn');
            const $form = $('#editTreatmentForm');
            
            // Disable submit button
            $updateBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Updating...');
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            // Prepare form data
            const formData = new FormData($form[0]);

            $.ajax({
                url: '{{ route("treatments.update", $treatment) }}',
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
                            text: 'Treatment updated successfully!',
                            confirmButtonColor: '#28a745',
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.href = '{{ route("treatments.show", $treatment) }}';
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
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please check the form fields and try again.',
                            confirmButtonColor: '#dc3545'
                        });
                    } else {
                        const message = xhr.responseJSON?.message || 'An error occurred while updating the treatment.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                complete: function() {
                    $updateBtn.prop('disabled', false).html('<i class="bi bi-check-circle"></i> Update Treatment');
                }
            });
        }
    </script>
@endsection