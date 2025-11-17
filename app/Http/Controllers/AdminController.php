<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Member;
use App\Models\Review;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->session()->get('is_admin')) {
            return Inertia::render('AdminPage', ['requiresAuth' => true]);
        }

        $members = Member::all()->map(function($m) {
            // try to include subscription name if relation exists
            $m->subscription_name = $m->subscription_id ?? null;
            return $m;
        });

        $reviews = Review::orderBy('created_at', 'desc')->get();

        return Inertia::render('AdminPage', [
            'members' => $members,
            'reviews' => $reviews,
        ]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = env('ADMIN_USERNAME', 'admin');
        $password = env('ADMIN_PASSWORD', 'secret123');

        if ($data['username'] === $username && $data['password'] === $password) {
            $request->session()->put('is_admin', true);
            return redirect('/admin');
        }

        return redirect('/admin')->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('is_admin');
        return redirect('/admin');
    }
}
