<body>
    @include('partials.topnav')
    @include('partials.leftnav')
    <main id="main" class="main">
        @include('partials.breadcrumbs')
        @yield('page')
    </main>
    @include('partials.footer')
</body>