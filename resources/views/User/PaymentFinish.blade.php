@extends('Layouts.layout')
@section('title','Pembayaran - DVNStore')
@section('content')
<div class="text-center py-5">
    <span class="material-symbols-outlined" style="font-size:5rem;color:var(--aqua)">check_circle</span>
    <h3 class="fw-bold mt-3" style="color: var(--dark-blue);">Pembayaran Diproses</h3>
    <p class="text-muted">Order ID: <code>{{ $orderId }}</code></p>
    <p>Status sementara: <strong>{{ $status ?? 'pending' }}</strong></p>
    <p class="text-muted small">Webhook Midtrans akan otomatis memperbarui status transaksi Anda.</p>
    <div class="mt-3">
        <a href="{{ route('unduhan.index') }}" class="btn btn-primary">Lihat Library</a>
        <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dashboard</a>
    </div>
</div>
@endsection
