{{-- resources/views/Layouts/Partials/footer.blade.php --}}
<footer class="mt-5 pt-5 pb-3 text-white">
    <div class="container">
        <div class="row gy-4">

            {{-- Logo & Sosial Media --}}
            <div class="col-md-4 text-center text-md-start">
                <img src="{{ asset('asset/image/logap.png') }}" alt="DVNStore Logo" width="72" height="72" class="mb-3">
                <p class="mb-2 fw-bold" style="font-family: 'Chakra Petch', sans-serif;">DVNStore {{ date('Y') }}</p>
                <div class="d-flex justify-content-center justify-content-md-start gap-3 fs-5">
                    <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>

            {{-- Navigasi Dashboard --}}
            <div class="col-md-4 text-center text-md-start">
                <h6 class="fw-bold text-uppercase mb-3" style="color: var(--aqua);">Dashboard</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/dashboard') }}" class="footer-link">Home</a></li>
                    <li><a href="{{ url('/dashboard/game') }}" class="footer-link">Games</a></li>
                    <li><a href="{{ url('/dashboard/app') }}" class="footer-link">Apps</a></li>
                </ul>
            </div>

            {{-- Top Kategori --}}
            <div class="col-md-4 text-center text-md-start">
                <h6 class="fw-bold text-uppercase mb-3" style="color: var(--aqua);">Top Kategori</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/top/game') }}" class="footer-link">Top Games</a></li>
                    <li><a href="{{ url('/top/app') }}" class="footer-link">Top Apps</a></li>
                </ul>
            </div>

        </div>

        {{-- Garis pemisah --}}
        <hr class="border-light mt-4">

        {{-- Footer bawah --}}
        <div class="row align-items-center text-center text-md-start">
            <div class="col-md-6 mb-2 mb-md-0">
                <small class="text-white-50">
                    <a href="#" class="footer-bottom-link">About Us</a> • 
                    <a href="#" class="footer-bottom-link">Security Policy</a> • 
                    <a href="#" class="footer-bottom-link">Bahasa Indonesia</a>
                </small>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small class="text-white-50">&copy; DVNStore {{ date('Y') }} | All Rights Reserved</small>
            </div>
        </div>
    </div>
</footer>
