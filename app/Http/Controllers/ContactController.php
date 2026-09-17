<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    function saveContact(Request $request)
    {
        Contact::create([
            'name' => $request->name,
            'email'=> $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->back();
    }
}
