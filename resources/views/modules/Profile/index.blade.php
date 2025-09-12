@extends('layouts.main')

@section('page_style')
    <style>
        .profile-card {
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #dee2e6;
            border-radius: 50%;
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
    My Profile
@endsection

@section('sub-title')
    User Profile
@endsection

@section('page')
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card profile-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title">My Profile</h5>
                            <a href="{{ route('profile.edit') }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit Profile
                            </a>
                        </div>

                        <div class="row">
                            <!-- Profile Picture Section -->
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6>Profile Picture</h6>
                                    <img src="{{ getUserProfilePic($user->profile_pic) }}" alt="Profile Picture" class="profile-pic">
                                    <p class="text-muted mt-2">{{ $user->name }}</p>
                                </div>
                            </div>

                            <!-- Profile Information -->
                            <div class="col-md-8">
                                <h6>Personal Information</h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-label">Full Name</div>
                                            <div class="info-value">{{ $user->name }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-label">Email Address</div>
                                            <div class="info-value">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Role</div>
                                            <div class="info-value">
                                                <span class="badge {{ $user->isAdmin() ? 'bg-danger' : ($user->role === 'manager' ? 'bg-info' : 'bg-secondary') }}">{{ ucfirst($user->role) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Account Status</div>
                                            <div class="info-value">
                                                <span class="badge bg-success">Active</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Account Information -->
                        <h6>Account Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Member Since</div>
                                    <div class="info-value">{{ $user->created_at ? $user->created_at->format('F d, Y') : 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-label">Last Updated</div>
                                    <div class="info-value">{{ $user->updated_at ? $user->updated_at->format('F d, Y H:i') : 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                Keep your profile information up to date for better security and communication.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection