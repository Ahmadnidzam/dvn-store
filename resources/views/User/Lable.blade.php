@extends('Layouts.layout')
@section('title', $platform->nama_platform . ' | DVNStore')
@section('content')
<a href="javascript:history.back()" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>

<div class="card shadow-sm mb-4"><div class="card-body">
    <div class="row g-4">
        <div class="col-md-3 text-center">
            <img src="{{ asset('storage/'.$platform->icon) }}" alt="{{ $platform->nama_platform }}"
                 style="width:200px;height:200px;object-fit:cover;border-radius:1rem">
        </div>
        <div class="col-md-9">
            <h2 class="fw-bold">{{ $platform->nama_platform }}</h2>
            <p class="mb-1">
                <span class="badge bg-secondary">{{ $platform->category }}</span>
                <span class="badge bg-info">{{ $platform->genre }}</span>
            </p>
            <p class="text-muted small mb-2">
                oleh <strong>{{ optional($platform->developer)->name }}</strong>
                @if(optional(optional($platform->developer)->developerProfile)->nama_studio)
                    - {{ $platform->developer->developerProfile->nama_studio }}
                @endif
            </p>
            <p class="fs-5 mb-3 rating-inline">
                <span class="material-symbols-outlined">star</span>
                {{ number_format($platform->rating, 2) }}
                <small class="text-muted">({{ $totalReviews }} ulasan)</small>
            </p>

            @if(Session::get('role') === 'user')
                @if($isOwned)
                    <button class="btn btn-secondary" disabled><span class="material-symbols-outlined">check</span> Sudah Dimiliki</button>
                    <a href="{{ route('unduhan.index') }}" class="btn btn-outline-primary">Buka Library</a>
                @elseif($platform->harga == 0)
                    <form action="{{ route('process.download', $platform->id) }}" method="POST" class="d-inline">@csrf
                        <button class="btn btn-success">Download Gratis</button>
                    </form>
                @else
                    <a href="{{ route('process.purchase', $platform->id) }}" class="btn btn-primary">
                        Beli Rp {{ number_format($platform->harga,0,',','.') }}
                    </a>
                @endif
            @elseif(Session::get('role') === 'developer')
                <div class="alert alert-info py-2 mb-0">Anda login sebagai developer, hanya bisa melihat detail.</div>
            @elseif(Session::get('role') === 'admin')
                <div class="alert alert-warning py-2 mb-0">Admin tidak dapat mengunduh atau membeli produk.</div>
            @endif
        </div>
    </div>
</div></div>

@if($platform->cuplikan)
<div class="card shadow-sm mb-4"><div class="card-body">
    <h5 class="fw-bold">Cuplikan</h5>
    <video controls class="w-100 rounded">
        <source src="{{ asset('storage/'.$platform->cuplikan) }}" type="video/mp4">
    </video>
</div></div>
@endif

<div class="card shadow-sm mb-4"><div class="card-body">
    <h5 class="fw-bold">Deskripsi</h5>
    <p style="white-space: pre-line;">{{ $platform->deskripsi }}</p>
</div></div>

<div class="card shadow-sm"><div class="card-body">
    <h5 class="fw-bold">Rating & Ulasan ({{ $totalReviews }})</h5>

    @foreach([5,4,3,2,1] as $star)
        @php $pct = $totalReviews > 0 ? ($starCounts[$star] / $totalReviews * 100) : 0; @endphp
        <div class="d-flex align-items-center gap-2 mb-1">
            <small style="width: 30px;">{{ $star }}<span class="material-symbols-outlined" style="font-size:.95rem;color:var(--warning)">star</span></small>
            <div class="progress flex-grow-1" style="height: 8px;">
                <div class="progress-bar bg-warning" style="width: {{ $pct }}%"></div>
            </div>
            <small class="text-muted" style="width: 40px;">{{ $starCounts[$star] }}</small>
        </div>
    @endforeach

    <hr>

    @if(Session::get('role') === 'user' && $isOwned && !$hasReviewed)
        <form action="{{ route('process.review', $platform->id) }}" method="POST" class="mb-4">@csrf
            <h6 class="fw-bold">Beri Ulasan</h6>
            <div class="mb-2">
                <label class="form-label">Rating</label>
                <select name="rating" class="form-select" required>
                    @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }} bintang</option>@endfor
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label">Komentar</label>
                <textarea name="komentar" class="form-control" rows="3" required minlength="3"></textarea>
            </div>
            <button class="btn btn-primary">Kirim Ulasan</button>
        </form>
        <hr>
    @endif

    @foreach($platform->reviews as $r)
        <div class="border rounded p-3 mb-2">
            <div class="d-flex justify-content-between">
                <div><strong>{{ $r->user->name }}</strong> <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small></div>
                <div class="rating-inline"><span class="material-symbols-outlined">star</span> {{ $r->rating }}</div>
            </div>
            <p class="mt-2 mb-2">{{ $r->komentar }}</p>
            @if(Session::get('role') === 'user' && Session::get('user_id') != $r->user_id)
                <form action="{{ route('review.helpful', $r->id) }}" method="POST" class="d-inline">@csrf
                    <button class="btn btn-sm btn-outline-success"><span class="material-symbols-outlined">thumb_up</span> Helpful ({{ $r->helpful_count }})</button>
                </form>
            @else
                <small class="text-muted helpful-inline"><span class="material-symbols-outlined">thumb_up</span> {{ $r->helpful_count }} helpful</small>
            @endif
        </div>
    @endforeach
</div></div>
@endsection
