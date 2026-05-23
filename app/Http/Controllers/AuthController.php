<?php

namespace App\Http\Controllers;

use App\Models\DeveloperProfile;
use App\Models\Pengguna;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function forbidden403() { return view('Error.Forbidden403'); }
    public function forbidden404() { return view('Error.Forbidden404'); }

    // -------------------- LOGIN --------------------
    public function login()
    {
        return view('Auth.Login');
    }

    public function validatelogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pass'  => 'required|string',
        ]);

        $user = Pengguna::where('email', $request->email)->first();
        if (!$user) {
            return back()->withInput()->with('error', 'Email tidak ditemukan');
        }
        if (!Hash::check($request->pass, $user->password)) {
            return back()->withInput()->with('error', 'Password salah!');
        }
        if ($user->isBlocked()) {
            return back()->withInput()->with('error', 'Akun Anda diblokir oleh admin.');
        }

        Session::put([
            'login'    => true,
            'name'     => $user->name,
            'role'     => $user->role,
            'status'   => $user->status,
            'user_id'  => $user->id,
        ]);

        // Redirect by role
        return match ($user->role) {
            'admin'     => redirect('/admin')->with('success', 'Selamat datang, ' . $user->name),
            'developer' => redirect('/developer')->with('success', 'Selamat datang developer, ' . $user->name),
            default     => redirect('/dashboard')->with('success', 'Selamat datang, ' . $user->name),
        };
    }

    // -------------------- REGISTER USER (customer) --------------------
    public function registerUser()
    {
        return view('Auth.RegisterUser');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:150|unique:penggunas,email',
            'pass'      => 'required|string|min:6',
            'konf_pass' => 'required|same:pass',
            'kode_unik' => 'required|string|min:4|max:50',
        ], [], [
            'pass'      => 'Password',
            'konf_pass' => 'Konfirmasi Password',
            'kode_unik' => 'Kode Unik',
        ]);

        Pengguna::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->pass),
            'role'      => 'user',
            'status'    => 'active',
            'kode_unik' => Hash::make($request->kode_unik),
        ]);

        return redirect('/login')->with('success', 'Akun customer berhasil dibuat! Silakan login.');
    }

    // -------------------- REGISTER DEVELOPER --------------------
    public function registerDeveloper()
    {
        return view('Auth.RegisterDeveloper');
    }

    public function storeDeveloper(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:150|unique:penggunas,email',
            'pass'                  => 'required|string|min:6',
            'konf_pass'             => 'required|same:pass',
            'kode_unik'             => 'required|string|min:4|max:50',
            'nama_studio'           => 'required|string|max:150',
            'deskripsi'             => 'nullable|string|max:1000',
            'website'               => 'nullable|url|max:255',
            'bank_name'             => 'required|string|max:100',
            'bank_account_number'   => 'required|string|max:50',
            'bank_account_holder'   => 'required|string|max:150',
        ], [], [
            'pass'      => 'Password',
            'konf_pass' => 'Konfirmasi Password',
        ]);

        DB::transaction(function () use ($request) {
            $dev = Pengguna::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->pass),
                'role'      => 'developer',
                'status'    => 'active',
                'kode_unik' => Hash::make($request->kode_unik),
            ]);

            DeveloperProfile::create([
                'pengguna_id'         => $dev->id,
                'nama_studio'         => $request->nama_studio,
                'deskripsi'           => $request->deskripsi,
                'website'             => $request->website,
                'bank_name'           => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_account_holder' => $request->bank_account_holder,
            ]);

            Wallet::create(['dev_id' => $dev->id, 'saldo' => 0]);
        });

        return redirect('/login')->with('success', 'Akun developer berhasil dibuat! Silakan login.');
    }

    // -------------------- FORGET PASSWORD --------------------
    public function forget()
    {
        return view('Auth.ForgetPassword');
    }

    public function validateforget(Request $request)
    {
        $pengguna = Pengguna::where('email', $request->email)->first();
        if (!$pengguna) {
            return back()->with('error', 'Email tidak ditemukan!');
        }
        if (!Hash::check($request->kode_unik, $pengguna->kode_unik)) {
            return back()->with('error', 'Kode Unik salah!');
        }
        Session::put('forget', true);
        Session::put('forget_email', $pengguna->email);
        return redirect('/forgetpassword');
    }

    public function forgetpassword()
    {
        if (!Session::has('forget')) {
            return redirect('/forgetpass')->with('error', 'Verifikasi email & kode unik dulu!');
        }
        return view('Auth.ForgetPassword1');
    }

    public function validateforgetpassword(Request $request)
    {
        $request->validate([
            'pass'      => 'required|min:6',
            'konf_pass' => 'required|same:pass',
        ]);

        $email = Session::get('forget_email');
        $pengguna = Pengguna::where('email', $email)->first();
        if (!$pengguna) {
            return redirect('/forbidden403');
        }
        $pengguna->update(['password' => Hash::make($request->pass)]);
        Session::forget(['forget', 'forget_email']);
        return redirect('/login')->with('success', 'Password telah diubah!');
    }

    // -------------------- LOGOUT --------------------
    public function logout()
    {
        Session::flush();
        return redirect('/login')->with('success', 'Anda telah keluar.');
    }
}
