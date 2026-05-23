@extends('Layouts.layout')
@section('title','Bayar Upload Fee - Developer')
@section('content')
<a href="{{ route('developer.platforms.index') }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
<div class="card shadow-sm">
    <div class="card-body text-center py-5">
        <span class="material-symbols-outlined" style="font-size:5rem;color:var(--aqua)">payments</span>
        <h3 class="fw-bold mt-3" style="color: var(--dark-blue);">Bayar Upload Fee</h3>
        <p class="text-muted">Produk: <strong>{{ $platform->nama_platform }}</strong></p>
        <h2 class="fw-bold my-3">Rp {{ number_format($fee,0,',','.') }}</h2>
        <p class="small text-muted">Kode Transaksi: <code>{{ $transaksi->kode_transaksi }}</code></p>

        <button id="pay-button" class="btn btn-success btn-lg mt-3">
            <span class="material-symbols-outlined align-middle">credit_card</span> Bayar Sekarang
        </button>
    </div>
</div>

@push('scripts')
<script src="https://app.{{ config('dvnstore.midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
        data-client-key="{{ $clientKey }}"></script>
<script>
document.getElementById('pay-button').addEventListener('click', function () {
    snap.pay(@json($snapToken), {
        onSuccess: function(){ window.location.href = "{{ route('developer.platforms.index') }}"; },
        onPending: function(){ window.location.href = "{{ route('developer.platforms.index') }}"; },
        onError:   function(){ alert('Pembayaran gagal.'); },
        onClose:   function(){ alert('Anda menutup popup pembayaran.'); }
    });
});
</script>
@endpush
@endsection
