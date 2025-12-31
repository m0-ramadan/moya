<?php

namespace App\Http\Controllers\Api;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactUsController extends Controller
{
    // 📌 Public – Store message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = ContactUs::create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully',
            'data' => $contact,
        ], 201);
    }
}
