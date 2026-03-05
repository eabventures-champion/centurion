<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Church;
use App\Models\User;

class CredentialsController extends Controller
{
    /**
     * Display a listing of church credentials.
     */
    public function index(Request $request)
    {
        // Check if credentials are unlocked in this session
        if (!$request->session()->has('credentials_unlocked')) {
            return redirect()->route('credentials.challenge');
        }

        // Get all churches with their associated pastor (leader)
        $churches = Church::with([
            'pastor' => function ($query) {
                $query->select('id', 'name', 'email', 'contact', 'plain_password');
            }
        ])->orderBy('name')->get();

        return view('credentials.index', compact('churches'));
    }

    /**
     * Show the password challenge view.
     */
    public function showChallenge()
    {
        return view('credentials.challenge');
    }

    /**
     * Verify the admin's password.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Access denied.']);
        }

        // Unlock credentials for this session
        $request->session()->put('credentials_unlocked', true);

        return redirect()->route('credentials.index');
    }
}
