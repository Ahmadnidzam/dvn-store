<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function index()
    {
        $developers = Pengguna::with(['developerProfile', 'wallet'])
            ->withCount('platforms')
            ->where('role', 'developer')->latest()->paginate(30);
        return view('Admin.Developers.Index', compact('developers'));
    }

    public function show($id)
    {
        $dev = Pengguna::with(['developerProfile', 'wallet', 'platforms', 'withdraws'])
            ->where('role', 'developer')->findOrFail($id);
        return view('Admin.Developers.Show', compact('dev'));
    }

    public function block(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $dev = Pengguna::where('role', 'developer')->findOrFail($id);
        $dev->update([
            'status'         => 'blocked',
            'blocked_at'     => now(),
            'blocked_reason' => $request->reason,
        ]);
        return back()->with('success', "Developer {$dev->name} diblokir.");
    }

    public function unblock($id)
    {
        $dev = Pengguna::findOrFail($id);
        $dev->update(['status' => 'active', 'blocked_at' => null, 'blocked_reason' => null]);
        return back()->with('success', "Developer {$dev->name} diaktifkan.");
    }
}
