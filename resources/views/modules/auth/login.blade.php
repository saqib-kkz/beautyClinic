@extends('layouts.main')
@section('page_style')
@endsection

@section('page')
<div class="row justify-content-center">
    <div class="col-lg-4 offset-lg-1 mb-5 mb-lg-0">
        <div class="card cascading-right">
            <div class="card-body p-5 shadow-5 text-center">
                <img src="{{ asset('images/logo.png') }}" height="100" alt="Clinic Logo" />
                <h3 class="fw-bold my-5">Clinic Login</h3>
                
                {{-- Display Success/Error Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('failed'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('failed') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <form id="login_form" method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <div class="input-group mb-4">
                        <input 
                            type="email" 
                            name="identity" 
                            class="form-control @error('identity') is-invalid @enderror" 
                            required 
                            placeholder="Email Address" 
                            value="{{ old('identity') }}"
                            autocomplete="email"
                            autofocus
                        />
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        @error('identity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-outline input-group mb-4">
                        <input 
                            type="password" 
                            name="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            required 
                            placeholder="Password"
                            autocomplete="current-password"
                        />
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check d-flex justify-content-center mb-4">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember" 
                            value="1" 
                            class="form-check-input me-2" 
                            {{ old('remember') ? 'checked' : '' }}
                        />
                        <label class="form-check-label" for="remember">Remember Me</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block mb-4 w-100">
                        <i class="fas fa-key me-2"></i>
                        <span class="bigger-110">Login</span>
                    </button>
                </form>
                
                {{-- Demo Credentials --}}
                <div class="mt-4">
                    <small class="text-muted">
                        <strong>Demo Credentials:</strong><br>
                        Admin: admin@clinic.com / password123<br>
                        Staff: staff@clinic.com / password123
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 inset-lg-1 mb-5 mb-lg-0 d-none d-sm-none d-md-block d-lg-block">
        <div class="login-bg img-container rounded shadow" style="background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); height: 400px;"></div>
    </div>
</div>
@endsection