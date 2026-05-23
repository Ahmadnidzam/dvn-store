<?php

namespace App\Http\Middleware;

use App\Models\Pengguna;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Cek apakah user login & punya role yang sesuai.
 * Pemakaian di route: ->middleware('role:admin') atau 'role:developer,admin'
 *
 * Catatan: Aplikasi ini pakai session-based auth, BUKAN Laravel Auth.
 * Field yang dipakai: Session::get('user_id'), 'login', 'role'.
 *
 * Status (active/blocked) selalu di-cek FRESH dari DB tiap request supaya
 * pemblokiran oleh admin langsung berlaku — tidak menunggu user login ulang.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Session::has('user_id') || !Session::has('login')) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Fetch fresh dari DB — jangan percaya session.
        $user = Pengguna::find(Session::get('user_id'));

        if (!$user) {
            Session::flush();
            return redirect('/login')->with('error', 'Sesi tidak valid. Silakan login ulang.');
        }

        if ($user->status === 'blocked') {
            Session::flush();
            $reason = $user->blocked_reason ? " (Alasan: {$user->blocked_reason})" : '';
            return redirect('/login')->with('error', "Akun Anda telah diblokir oleh admin{$reason}.");
        }

        // Sinkronkan session dengan DB (jaga-jaga role berubah)
        if (Session::get('role') !== $user->role) {
            Session::put('role', $user->role);
        }
        Session::put('status', $user->status);

        if (!in_array($user->role, $roles, true)) {
            return redirect('/forbidden403');
        }

        return $next($request);
    }
}
