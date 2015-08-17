<?php

namespace ICT\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use ICT\Http\Requests\FeedbackRequest;
use ICT\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    /**
     * Display a feedback form.
     *
     * @return Response
     */
    public function index()
    {
        return view('feedback.index');
    }

    /**
     * Send feedback.
     *
     * @return Response
     */
    public function send(FeedbackRequest $request)
    {
        $data = [
            'name'        => $request->name,
            'email'       => $request->email,
            'description' => $request->description,
        ];

        Mail::send('emails.feedback', $data, function($message)
        {
            $message->from('noreply@wichitaweso.me', 'Wichitasome!');
            $message->to('christianbtaylor@gmail.com')->subject('New Feedback!');
        });

        return redirect('/')->with('message', '<strong>You\'re awesome!</strong> Thanks for the feedback, it means a lot.');
    }
}
