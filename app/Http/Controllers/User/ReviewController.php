<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\Platform;
use App\Models\Review;
use App\Models\ReviewHelpful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ReviewController extends Controller
{
    public function store(Request $request, $platformId)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|min:3|max:1000',
        ]);

        $userId = Session::get('user_id');

        // Harus sudah memiliki produk
        $owned = Download::where('user_id', $userId)->where('platform_id', $platformId)->exists();
        if (!$owned) {
            return back()->with('error', 'Anda harus memiliki produk ini terlebih dahulu untuk memberi review.');
        }

        DB::transaction(function () use ($userId, $platformId, $request) {
            $existing = Review::where('user_id', $userId)
                ->where('platform_id', $platformId)->first();

            if ($existing) {
                // Update rating + komentar saja — jangan reset helpful_count.
                $existing->update([
                    'rating'   => $request->rating,
                    'komentar' => $request->komentar,
                ]);
            } else {
                Review::create([
                    'user_id'       => $userId,
                    'platform_id'   => $platformId,
                    'rating'        => $request->rating,
                    'komentar'      => $request->komentar,
                    'helpful_count' => 0,
                ]);
            }

            // Recompute rating avg
            $avg = Review::where('platform_id', $platformId)->avg('rating') ?? 0;
            Platform::where('id', $platformId)->update(['rating' => round($avg, 2)]);
        });

        return back()->with('success', 'Ulasan berhasil dikirim!');
    }

    public function toggleHelpful($reviewId)
    {
        $userId = Session::get('user_id');

        DB::transaction(function () use ($reviewId, $userId) {
            $review = Review::whereKey($reviewId)->lockForUpdate()->firstOrFail();

            $existing = ReviewHelpful::where('review_id', $review->id)
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $existing->delete();
                $review->helpful_count = max(0, $review->helpful_count - 1);
            } else {
                ReviewHelpful::create(['review_id' => $review->id, 'user_id' => $userId]);
                $review->helpful_count = $review->helpful_count + 1;
            }
            $review->save();
        });

        return back();
    }
}
