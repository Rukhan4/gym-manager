<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::latest()->get();

        return Inertia::render('Reviews', [
            'reviews' => $reviews
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
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
