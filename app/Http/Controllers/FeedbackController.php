<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('pages.feedback');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'type' => 'required|in:feedback,bug_report,feature_request,support',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'type' => $request->type,
        ]);

        return redirect()->route('pages.feedback')
            ->with('success', 'Thank you for your feedback! We will review it shortly.');
    }
}