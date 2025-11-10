<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Member;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index()
    {
        return Inertia::render('Subscriptions', [
            'subscriptions' => Subscription::all(),
            'members' => Member::with('subscription')->get(),
        ]);
    }
}
