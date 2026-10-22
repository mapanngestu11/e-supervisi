<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PinController extends Controller
{
    public function show()
    {
        return view('pin.verify');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'string'],
        ]);

        $correctPin = env('APP_SECURITY_PIN', '112233');

        if ($request->input('pin') === $correctPin) {
            session(['is_locked' => false, 'last_activity' => time()]);
            return redirect()->intended(route('dashboard'))->with('status', 'Session unlocked.');
        }

        return back()->withErrors(['pin' => 'PIN salah. Silakan coba lagi.']);
    }
}
