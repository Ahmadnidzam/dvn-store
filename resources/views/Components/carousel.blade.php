{{-- resources/views/Layouts/Partials/carousel.blade.php --}}
<div id="mainCarousel" class="carousel slide shadow-lg rounded-4 overflow-hidden" data-bs-ride="carousel">
    {{-- Indicators --}}
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
    </div>

    {{-- Slides --}}
    <div class="carousel-inner">
        {{-- Slide 1 --}}
        <div class="carousel-item active">
            <div class="position-relative">
                <div class="position-absolute top-50 start-0 translate-middle-y text-white px-4 px-lg-5" style="max-width: 680px;">
                    <span class="badge bg-info mb-3">Curated Storefront</span>
                    <h2 class="fw-bold mb-3">Featured Games Collection</h2>
                    <p class="fs-5 mb-4">Discover game releases, community favorites, and fresh uploads from DVNStore developers.</p>
                    <a href="{{ url('/dashboard/game') }}" class="btn btn-light btn-lg px-4">
                        Explore Games
                    </a>
                </div>
            </div>
        </div>

        {{-- Slide 2 --}}
        <div class="carousel-item">
            <div class="position-relative">
                <div class="position-absolute top-50 start-0 translate-middle-y text-white px-4 px-lg-5" style="max-width: 680px;">
                    <span class="badge bg-info mb-3">Apps & Tools</span>
                    <h2 class="fw-bold mb-3">Powerful Applications</h2>
                    <p class="fs-5 mb-4">Find practical tools, creative apps, and utilities for everyday workflows.</p>
                    <a href="{{ url('/dashboard/app') }}" class="btn btn-light btn-lg px-4">
                        Get Apps
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Controls --}}
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
