<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Waad\ProfanityFilter\Facades\ProfanityFilter;
use App\Models\Review;

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
        'email' => 'required|email',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string',
    ]);

    // Check for profanity
    if (ProfanityFilter::hasProfanity($validated['comment'])) {
        return back()->withErrors(['comment' => 'Your comment contains inappropriate language.']);
    }

    Review::create($validated);

    return redirect()->back()->with('success', 'Review added successfully!');
    }

    public function destroy($id)
    {
        Review::findOrFail($id)->delete();
        return redirect('/reviews')->with('success', 'Review deleted successfully!');
    }
}
