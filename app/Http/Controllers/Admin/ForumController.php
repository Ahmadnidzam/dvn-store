<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;

class ForumController extends Controller
{
    public function index()
    {
        $posts = ForumPost::with('user')->latest()->paginate(30);
        return view('Admin.Forum.Index', compact('posts'));
    }

    public function hide($id)
    {
        $p = ForumPost::findOrFail($id);
        $p->update(['is_hidden' => true]);
        return back()->with('success', 'Post disembunyikan.');
    }

    public function unhide($id)
    {
        $p = ForumPost::findOrFail($id);
        $p->update(['is_hidden' => false]);
        return back()->with('success', 'Post ditampilkan kembali.');
    }

    public function destroy($id)
    {
        $p = ForumPost::findOrFail($id);
        $p->delete();
        return back()->with('success', 'Post dihapus.');
    }
}
