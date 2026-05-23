<h3 class="fw-bold section-title mb-3">Popular Categories</h3>
    <div class="row g-4">
        <div class="col-lg-6 col-md-6">
            <div class="card card-hover card-medium rounded-4 overflow-hidden position-relative">
                <div class="position-absolute w-100 h-100" style="background: linear-gradient(135deg, #002f6c 0%, #00d8d8 100%);"></div>
                <div class="overlay-gradient"></div>
                <div class="card-body d-flex flex-column justify-content-end position-relative text-white p-4">
                    <span class="material-symbols-outlined category-icon mb-2" style="font-size: 3rem; opacity: 0.9;">apps</span>
                    <h5 class="fw-bold mb-2">Top Apps</h5>
                    <p class="mb-3 small opacity-75">Tools to boost your workflow</p>
                    <a href="{{ url('/top/app') }}" class="btn btn-sm btn-light px-3 align-self-start">
                        Explore
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6">
            <div class="card card-hover card-medium rounded-4 overflow-hidden position-relative">
                <div class="position-absolute w-100 h-100" style="background: linear-gradient(135deg, #23395d 0%, #f5b400 100%);"></div>
                <div class="overlay-gradient"></div>
                <div class="card-body d-flex flex-column justify-content-end position-relative text-white p-4">
                    <span class="material-symbols-outlined category-icon mb-2" style="font-size: 3rem; opacity: 0.9;">stadia_controller</span>
                    <h5 class="fw-bold mb-2">Top Games</h5>
                    <p class="mb-3 small opacity-75">High-octane gaming experience</p>
                    <a href="{{ url('/top/game') }}" class="btn btn-sm btn-light px-3 align-self-start">
                        Play Now
                    </a>
                </div>
            </div>
        </div>
    </div>
