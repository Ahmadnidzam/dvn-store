<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;

class PlatformController extends Controller
{
    public function index(Request $request)
    {
        $query = Platform::with('developer')->latest();
        if ($request->category && in_array($request->category, ['app', 'game'])) {
            $query->where('category', $request->category);
        }
        $platforms = $query->paginate(30)->withQueryString();
        return view('Admin.Platforms.Index', compact('platforms'));
    }

    public function show($id)
    {
        $platform = Platform::with(['developer.developerProfile', 'reviews.user'])->findOrFail($id);
        return view('Admin.Platforms.Show', compact('platform'));
    }

    public function takedown(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $platform = Platform::findOrFail($id);
        $platform->update([
            'is_taken_down'      => true,
            'taken_down_at'      => now(),
            'taken_down_reason'  => $request->reason,
            'is_published'       => false,
        ]);
        return back()->with('success', "Platform '{$platform->nama_platform}' di-takedown.");
    }

    public function restore($id)
    {
        $platform = Platform::findOrFail($id);
        $platform->update([
            'is_taken_down'      => false,
            'taken_down_at'      => null,
            'taken_down_reason'  => null,
            'is_published'       => $platform->scan_status === 'clean',
        ]);
        return back()->with('success', "Platform '{$platform->nama_platform}' dipulihkan.");
    }
}
