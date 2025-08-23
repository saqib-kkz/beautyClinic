<div class="pagetitle">
    <h1>@hasSection('title') @yield('title') @endif</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active">@hasSection('sub-title') @yield('sub-title') @endif</li>
        </ol>
    </nav>
</div>