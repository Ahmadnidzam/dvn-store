@extends('Layouts.layout')
@section('title','Checkout - DVNStore')
@section('content')
<a href="{{ url()->previous() }}" class="btn-back"><span class="material-symbols-outlined">arrow_back</span> Kembali</a>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                @if($platform->icon)<img src="{{ asset('storage/'.$platform->icon) }}" class="object-thumb" style="width:100px;height:100px" alt="{{ $platform->nama_platform }}">@endif
                <h3 class="fw-bold mt-3">{{ $platform->nama_platform }}</h3>
                <p class="text-muted">{{ $platform->genre }}</p>
                <h2 class="fw-bold my-3 text-brand">Rp {{ number_format($platform->harga,0,',','.') }}</h2>
                <p class="small text-muted">Kode: <code>{{ $transaksi->kode_transaksi }}</code></p>

                <button id="pay-button" class="btn btn-success btn-lg mt-3 w-100">
                    <span class="material-symbols-outlined align-middle">credit_card</span> Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://app.{{ config('dvnstore.midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js"
        data-client-key="{{ $clientKey }}"></script>
<script>
document.getElementById('pay-button').addEventListener('click', function () {
    snap.pay(@json($snapToken), {
        onSuccess: function(){ window.location.href = "{{ route('unduhan.index') }}"; },
        onPending: function(){ window.location.href = "{{ route('unduhan.index') }}"; },
        onError:   function(){ alert('Pembayaran gagal.'); },
        onClose:   function(){ alert('Anda menutup popup pembayaran.'); }
    });
});
</script>
@endpush
@endsection
