@extends('Layouts.layout')
@section('title','Request Withdraw - Developer')
@section('content')
<a href="{{ route('developer.withdraws.index') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
<h3 class="fw-bold mb-3" style="color: var(--dark-blue);">Request Withdraw</h3>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
            <p class="mb-1 text-muted small">Saldo tersedia:</p>
            <h3 class="fw-bold">Rp {{ number_format($wallet->saldo,0,',','.') }}</h3>
            <p class="text-muted small">Minimum: Rp {{ number_format($minWithdraw,0,',','.') }}</p>
            <hr>
            <p class="mb-1"><strong>Bank Tujuan:</strong></p>
            @if($dev->developerProfile)
                <p class="mb-1">{{ strtoupper($dev->developerProfile->bank_name) }}</p>
                <p class="mb-1">{{ $dev->developerProfile->bank_account_number }}</p>
                <p class="mb-0">a.n. {{ $dev->developerProfile->bank_account_holder }}</p>
            @else
                <p class="text-danger">Profil bank belum lengkap.</p>
            @endif
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('developer.withdraws.store') }}">@csrf
            <div class="mb-3"><label class="form-label">Jumlah Withdraw (Rp)</label>
                <input type="number" name="amount" class="form-control form-control-lg" min="{{ $minWithdraw }}" max="{{ $wallet->saldo }}" required></div>
            <button class="btn btn-success w-100">Kirim Request via Midtrans IRIS</button>
        </form>
        </div></div>
    </div>
</div>
@endsection
