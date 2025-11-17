<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    // Return all members in Subscriptions page
    public function list()
    {
        return Member::with('subscription')->get();
    }

    // Store a new member
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:members,email',
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        Member::create($request->all());

        
        return redirect()->back()->with('success', 'Member added successfully!');
    }

    // Update a member
    public function update(Request $request, Member $member)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        $member->update($request->all());

       
        return redirect()->back()->with('success', 'Member updated successfully!');
    }

    // Delete a member
    public function destroy(Request $request, Member $member)
    {
        if (! $request->session()->get('is_admin')) {
            abort(403);
        }

        $member->delete();

        return redirect()->back()->with('success', 'Member deleted successfully!');
    }
}
