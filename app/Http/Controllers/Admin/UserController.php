<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = Pengguna::where('role', 'user')->latest()->paginate(30);
        return view('Admin.Users.Index', compact('users'));
    }

    public function block(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $u = Pengguna::where('role', 'user')->findOrFail($id);
        $u->update([
            'status'         => 'blocked',
            'blocked_at'     => now(),
            'blocked_reason' => $request->reason,
        ]);
        return back()->with('success', "User {$u->name} diblokir.");
    }

    public function unblock($id)
    {
        $u = Pengguna::findOrFail($id);
        $u->update(['status' => 'active', 'blocked_at' => null, 'blocked_reason' => null]);
        return back()->with('success', "User {$u->name} diaktifkan kembali.");
    }
}
