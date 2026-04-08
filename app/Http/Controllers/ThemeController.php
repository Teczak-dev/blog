<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    /**
     * Update user's theme preference
     */
    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,system'
        ]);

        if (Auth::check()) {
            Auth::user()->update([
                'theme_preference' => $request->theme
            ]);
        } else {
            // Store in session for guests
            session(['theme_preference' => $request->theme]);
        }

        return response()->json([
            'success' => true,
            'theme' => $request->theme
        ]);
    }
}
