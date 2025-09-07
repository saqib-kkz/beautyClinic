@extends('layouts.main')

@section('page_style')
    <style>
        .info-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
        }
        .info-value {
            color: #212529;
            margin-bottom: 15px;
        }
        .staff-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9em;
        }
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }
        .treatments-table th {
            background-color: #f8f9fa;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .btn-group-custom .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('title')
    Staff Management
@endsection

@section('sub-title')
    Staff Details
@endsection

@section('page')
    <section class="section">
        <div class="row">
            <!-- Staff Information ---->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Staff Information</h5>
                            <span class="staff-status status-{{ $staff->is_active ? 'active' : 'inactive' }}">
                                {{ $staff->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Full Name</div>
                                    <div class="info-value">
                                        <strong>{{ $staff->name }}</strong>
                                    </div>

                                    <div class="info-label">Email Address</div>
                                    <div class="info-value">
                                        <i class="bi bi-envelope"></i> {{ $staff->email }}
                                    </div>

                                    <div class="info-label">Role</div>
                                    <div class="info-value">
                                        <span class="badge {{ $staff->role === 'manager' ? 'bg-info' : 'bg-secondary' }}">
                                            {{ ucfirst($staff->role) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-label">Member Since</div>
                                    <div class="info-value">
                                        <i class="bi bi-calendar"></i> {{ $staff->created_at->format('M d, Y') }}
                                    </div>

                                    <div class="info-label">Last Updated</div>
                                    <div class="info-value">
                                        <i class="bi bi-clock"></i> {{ $staff->updated_at->format('M d, Y H:i') }}
                                    </div>

                                    <div class="info-label">Account Status</div>
                                    <div class="info-value">
                                        <span class="staff-status status-{{ $staff->is_active ? 'active' : 'inactive' }}">
                                            {{ $staff->is_active ? 'Active Account' : 'Inactive Account' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Treatments -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Recent Treatments</h5>

                        @if($treatments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped treatments-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Client</th>
                                            <th>Treatment</th>
                                            <th>Products Used</th>
                                            <th>Value</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($treatments->take(10) as $treatment)
                                            <tr>
                                                <td>{{ $treatment->treatment_date->format('M d, Y') }}</td>
                                                <td>
                                                    <strong>{{ $treatment->client->full_name }}</strong>
                                                    @if($treatment->client->contact_number)
                                                        <br><small class="text-muted">{{ $treatment->client->contact_number }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $treatment->treatment_name }}</strong>
                                                    @if($treatment->treatment_reason)
                                                        <br><small class="text-muted">{{ $treatment->treatment_reason }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $treatment->treatmentProducts->count() }} products</span>
                                                    <br><small class="text-muted">Qty: {{ $treatment->treatmentProducts->sum('quantity_used') }}</small>
                                                </td>
                                                <td>
                                                    <strong>AED {{ number_format($treatment->treatmentProducts->sum('total_price') * 1.05, 2) }}</strong>
                                                    <br><small class="text-muted">(inc. VAT)</small>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $treatment->status === 'completed' ? 'bg-success' : 'bg-warning' }}">
                                                        {{ ucfirst($treatment->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('treatments.show', $treatment) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($treatments->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('treatments.index', ['therapist' => $staff->name]) }}" class="btn btn-outline-primary">
                                        View All Treatments ({{ $treatments->count() }})
                                    </a>
                                </div>
                            @endif
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-clipboard" style="font-size: 3rem;"></i>
                                <p>No treatments performed yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stats & Actions Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Actions</h5>

                        <div class="btn-group-custom d-grid gap-2">
                            <button class="btn btn-warning" onclick="editStaff({{ $staff->id }})">
                                <i class="bi bi-pencil-square"></i> Edit Staff Member
                            </button>

                            <button class="btn {{ $staff->is_active ? 'btn-warning' : 'btn-success' }}" 
                                    onclick="toggleStatus({{ $staff->id }})">
                                <i class="bi bi-{{ $staff->is_active ? 'pause' : 'play' }}"></i> 
                                {{ $staff->is_active ? 'Deactivate' : 'Activate' }} Account
                            </button>

                            @if($treatments->count() === 0)
                                <button class="btn btn-danger" onclick="deleteStaff({{ $staff->id }})">
                                    <i class="bi bi-trash"></i> Delete Staff Member
                                </button>
                            @endif

                            <hr>

                            <a href="{{ route('staff.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Staff List
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Staff Statistics -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Staff Statistics</h5>

                        <div class="stats-card">
                            <div class="row text-center">
                                <div class="col-12 mb-3">
                                    <h6>Total Treatments</h6>
                                    <h3>{{ $treatments->count() }}</h3>
                                </div>
                                <div class="col-6">
                                    <h6>This Month</h6>
                                    <h4>{{ $treatments->whereMonth('treatment_date', now()->month)->count() }}</h4>
                                </div>
                                <div class="col-6">
                                    <h6>This Year</h6>
                                    <h4>{{ $treatments->whereYear('treatment_date', now()->year)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                @if($staff->stockAdjustments->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Recent Stock Activities</h5>
                            
                            <div class="list-group list-group-flush">
                                @foreach($staff->stockAdjustments->take(5) as $adjustment)
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $adjustment->type }}</h6>
                                            <small>{{ $adjustment->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1">{{ $adjustment->notes }}</p>
                                        <small class="text-muted">
                                            Quantity: {{ $adjustment->quantity > 0 ? '+' : '' }}{{ $adjustment->quantity }}
                                        </small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('page_script')
    <script>
        function editStaff(id) {
            $.ajax({
                url: "{{ route('staff.edit', $staff->id) }}",
                type: "GET",
                success: function(response) {
                    if (response.success) {
                        var staff = response.staff;
                        // Redirect to staff list with edit modal
                        sessionStorage.setItem('editStaffData', JSON.stringify(staff));
                        window.location.href = "{{ route('staff.index') }}#edit-" + id;
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
                        text: 'Error loading staff data. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }

        function toggleStatus(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will change the staff member\'s account status',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('staff.toggleStatus', $staff->id) }}",
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
                                }).then(() => {
                                    location.reload();
                                });
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

        @if($treatments->count() === 0)
        function deleteStaff(id) {
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
                        url: "{{ route('staff.destroy', $staff->id) }}",
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
                                }).then(() => {
                                    window.location.href = "{{ route('staff.index') }}";
                                });
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
        @endif
    </script>
@endsection