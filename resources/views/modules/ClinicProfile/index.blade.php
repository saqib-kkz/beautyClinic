@extends('layouts.main')

@section('page_style')
    <style>
        .profile-card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo-display {
            width: 150px;
            height: 150px;
            object-fit: contain;
            border: 2px solid #dee2e6;
            border-radius: 8px;
        }
        .info-item {
            margin-bottom: 1rem;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }
        .info-value {
            color: #212529;
        }
    </style>
@endsection

@section('title')
    Clinic Profile
@endsection

@section('sub-title')
    Clinic Information
@endsection

@section('page')
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card profile-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">Clinic Profile</h5>
                            <a href="{{ route('clinic-profile.edit') }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit Profile
                            </a>
                        </div>

                        <div class="row">
                            <!-- Logo Section -->
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6>Clinic Logo</h6>
                                    <img src="{{ $profile->logo_url }}" alt="Clinic Logo" class="logo-display">
                                </div>
                            </div>

                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <h6>Basic Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Clinic Name</div>
                                            <div class="info-value">{{ $profile->clinic_name }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Phone</div>
                                            <div class="info-value">{{ $profile->phone ?: 'Not provided' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Email</div>
                                            <div class="info-value">{{ $profile->email ?: 'Not provided' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Website</div>
                                            <div class="info-value">
                                                @if($profile->website)
                                                    <a href="{{ $profile->website }}" target="_blank">{{ $profile->website }}</a>
                                                @else
                                                    Not provided
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Address and Description -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Address</div>
                                    <div class="info-value">{{ $profile->address ?: 'Not provided' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Description</div>
                                    <div class="info-value">{{ $profile->description ?: 'Not provided' }}</div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Legal Information -->
                        <h6>Legal Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Tax Number</div>
                                    <div class="info-value">{{ $profile->tax_number ?: 'Not provided' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">License Number</div>
                                    <div class="info-value">{{ $profile->license_number ?: 'Not provided' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($profile->created_at)
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                Profile created on {{ $profile->created_at->format('F d, Y') }}
                                @if($profile->updated_at && $profile->updated_at->ne($profile->created_at))
                                    • Last updated on {{ $profile->updated_at->format('F d, Y H:i') }}
                                @endif
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection