<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::leftJoin('members', 'reviews.email', '=', 'members.email')
        ->select('reviews.*', DB::raw('CASE WHEN members.id IS NOT NULL THEN true ELSE false END as verified'))
        ->orderBy('reviews.created_at', 'desc')
        ->get();


        return Inertia::render('Reviews', [
            'reviews' => $reviews,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'string',
        ]);

        Review::create($validated);

        return redirect('/reviews')->with('success', 'Review submitted successfully!');
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();
        return redirect('/reviews')->with('success', 'Review deleted successfully!');
    }
}
