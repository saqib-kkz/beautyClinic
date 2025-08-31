    @if(Auth::user())
        <footer id="footer" class="footer">
            <div class="copyright">
                &copy; Copyright <strong><span>Swan Aesthetic Clinic.</span></strong> All Rights Reserved
            </div>
            <div class="credits">
                Swan Aesthetic Clinic - V 1.0 &copy; {{date('Y')}}
            </div>
        </footer>
    @endif

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Library: Jquery -->
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script> --}}

    <!-- Library: Bootstrap 5 -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> --}}

    <!-- Vendor: JS Files -->
    <script src="{{getadminasset('vendor/apexcharts/apexcharts.min.js')}}"></script>
    <script src="{{getadminasset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{getadminasset('vendor/chart.js/chart.umd.js')}}"></script>
    <script src="{{getadminasset('vendor/echarts/echarts.min.js')}}"></script>
    <script src="{{getadminasset('vendor/quill/quill.min.js')}}"></script>
    <script src="{{getadminasset('vendor/simple-datatables/simple-datatables.js')}}"></script>
    <script src="{{getadminasset('vendor/tinymce/tinymce.min.js')}}"></script>
    <script src="{{getadminasset('vendor/php-email-form/validate.js')}}"></script>

    <!-- Custom: JS File -->
    <script src="{{getadminasset('js/main.js')}}"></script>

    @yield('page_script')
    @yield('page_modal')