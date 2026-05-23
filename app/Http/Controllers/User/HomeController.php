<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Platform;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    // Dashboard utama
    public function utama()
    {
        $games = Platform::available()->where('category', 'game')->latest()->limit(12)->get();
        $apps  = Platform::available()->where('category', 'app')->latest()->limit(12)->get();
        return view('User.DashboardUtama', compact('games', 'apps'));
    }

    public function games()
    {
        $games = Platform::available()->where('category', 'game')->latest()->get();
        return view('User.DashboardGames', compact('games'));
    }

    public function apps()
    {
        $apps = Platform::available()->where('category', 'app')->latest()->get();
        return view('User.DashboardApps', compact('apps'));
    }

    public function topgame()
    {
        $games = Platform::available()->where('category', 'game')->orderByDesc('rating')->get();
        return view('User.TopGames', compact('games'));
    }

    public function topapp()
    {
        $apps = Platform::available()->where('category', 'app')->orderByDesc('rating')->get();
        return view('User.TopApps', compact('apps'));
    }

    public function allgame()
    {
        $games = Platform::available()->where('category', 'game')->latest()->get();
        return view('User.AllGame', compact('games'));
    }

    public function allapp()
    {
        $apps = Platform::available()->where('category', 'app')->latest()->get();
        return view('User.AllApp', compact('apps'));
    }

    // Detail produk
    public function lable($id)
    {
        $platform = Platform::with(['developer.developerProfile', 'reviews.user'])
            ->where('is_taken_down', false)
            ->findOrFail($id);

        $isOwned = false; $hasReviewed = false;
        if (Session::has('user_id')) {
            $userId = Session::get('user_id');
            $isOwned = $platform->downloads()->where('user_id', $userId)->exists();
            $hasReviewed = Review::where('user_id', $userId)->where('platform_id', $id)->exists();
        }

        $starCounts = [
            5 => $platform->reviews->where('rating', 5)->count(),
            4 => $platform->reviews->where('rating', 4)->count(),
            3 => $platform->reviews->where('rating', 3)->count(),
            2 => $platform->reviews->where('rating', 2)->count(),
            1 => $platform->reviews->where('rating', 1)->count(),
        ];
        $totalReviews = $platform->reviews->count();

        return view('User.Lable', compact('platform', 'isOwned', 'hasReviewed', 'starCounts', 'totalReviews'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $results = $q === ''
            ? collect()
            : Platform::available()
                ->where(function ($w) use ($q) {
                    $w->where('nama_platform', 'like', "%{$q}%")
                      ->orWhere('genre', 'like', "%{$q}%");
                })
                ->latest()->get();
        return view('User.Search', ['search' => $results, 'query' => $q]);
    }

    // Profile
    public function profile()
    {
        $user = Pengguna::withCount('downloads')->find(Session::get('user_id'));
        return view('User.Profile', compact('user'));
    }

    public function editprofile()
    {
        $user = Pengguna::find(Session::get('user_id'));
        return view('User.EditProfile', compact('user'));
    }

    public function updateprofile(Request $request)
    {
        $user = Pengguna::findOrFail(Session::get('user_id'));
        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => "required|email|max:150|unique:penggunas,email,{$user->id}",
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $avatarPath = $user->avatar;
        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $avatarPath = $request->file('avatar')->store('images/avatars', 'public');
        }

        $user->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'avatar' => $avatarPath,
        ]);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }
}
