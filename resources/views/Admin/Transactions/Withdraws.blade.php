@extends('Layouts.layout')
@section('title','Withdraw - Admin')
@section('content')
<h3 class="fw-bold mb-3" style="color: var(--dark-blue);">Riwayat Withdraw</h3>
<div class="card shadow-sm"><div class="card-body table-responsive">
<table class="table align-middle table-sm">
<thead><tr><th>#</th><th>Developer</th><th class="text-end">Amount</th><th>Bank</th><th>Status</th><th>IRIS Ref</th><th>Processed</th></tr></thead>
<tbody>
@forelse($withdraws as $w)
<tr>
    <td>{{ $w->id }}</td>
    <td>{{ $w->developer->name ?? '-' }}</td>
    <td class="text-end">Rp {{ number_format($w->amount,0,',','.') }}</td>
    <td>{{ strtoupper($w->bank_snapshot['bank_name'] ?? '-') }} {{ $w->bank_snapshot['bank_account_number'] ?? '' }}</td>
    <td><span class="badge bg-info">{{ $w->status }}</span></td>
    <td><code>{{ $w->iris_payout_reference_no ?: '-' }}</code></td>
    <td>{{ optional($w->processed_at)->format('d M Y H:i') }}</td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted">Belum ada withdraw.</td></tr>
@endforelse
</tbody></table>
{{ $withdraws->links() }}
</div></div>
@endsection
