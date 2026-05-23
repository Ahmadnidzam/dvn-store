@extends('Layouts.layout')
@section('title','Transaksi - Admin')
@section('content')
<h3 class="page-title"><span class="material-symbols-outlined">receipt_long</span> Semua Transaksi</h3>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table align-middle table-sm">
<thead><tr><th>Kode</th><th>Tipe</th><th>User</th><th>Platform</th><th class="text-end">Amount</th><th class="text-end">Fee</th><th>Status</th><th>Tanggal</th></tr></thead>
<tbody>
@forelse($transaksis as $t)
<tr>
    <td><code>{{ $t->kode_transaksi }}</code></td>
    <td><span class="badge bg-{{ $t->tipe==='purchase'?'success':'info' }}">{{ $t->tipe }}</span></td>
    <td>{{ $t->user->name ?? '-' }}</td>
    <td>{{ $t->platform->nama_platform ?? '-' }}</td>
    <td class="text-end">Rp {{ number_format($t->amount,0,',','.') }}</td>
    <td class="text-end">Rp {{ number_format($t->platform_fee,0,',','.') }}</td>
    <td><span class="badge bg-{{ ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary'][$t->status] ?? 'dark' }}">{{ $t->status }}</span></td>
    <td>{{ $t->created_at->format('d M Y H:i') }}</td>
</tr>
@empty
<tr><td colspan="8" class="text-center text-muted">Belum ada transaksi.</td></tr>
@endforelse
</tbody></table>
{{ $transaksis->links() }}
</div></div>
@endsection
