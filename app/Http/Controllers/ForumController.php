<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumPostHelpful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ForumController extends Controller
{
    public function index()
    {
        $posts = ForumPost::with('user')
            ->where('is_hidden', false)
            ->latest()->paginate(20);
        return view('Forum.Index', compact('posts'));
    }

    public function store(Request $request)
    {
        $request->validate(['content' => 'required|string|min:3|max:2000']);
        ForumPost::create([
            'user_id' => Session::get('user_id'),
            'content' => $request->content,
        ]);
        return back()->with('success', 'Pesan terkirim.');
    }

    public function toggleHelpful($postId)
    {
        $userId = Session::get('user_id');

        DB::transaction(function () use ($postId, $userId) {
            $post = ForumPost::whereKey($postId)->lockForUpdate()->firstOrFail();

            $existing = ForumPostHelpful::where('post_id', $post->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->delete();
                // Guard supaya tidak negatif (kalau counter race)
                $post->helpful_count = max(0, $post->helpful_count - 1);
            } else {
                ForumPostHelpful::create(['post_id' => $post->id, 'user_id' => $userId]);
                $post->helpful_count = $post->helpful_count + 1;
            }
            $post->save();
        });

        return back();
    }
}
