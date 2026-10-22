<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSessionLock
{
    public function handle(Request $request, Closure $next)
    {
        $timeoutMinutes = config('app.session_timeout_minutes', 15);
        $lastActivity = session('last_activity');
        $isLocked = session('is_locked', false);

        // Check if session should be locked due to timeout
        if ($lastActivity && !$isLocked) {
            $inactiveMinutes = (time() - $lastActivity) / 60;
            if ($inactiveMinutes >= $timeoutMinutes) {
                session(['is_locked' => true]);
                $isLocked = true;
            }
        }

        // Update last activity time if not locked
        if (!$isLocked) {
            session(['last_activity' => time()]);
        }

        // If locked and not verifying PIN, redirect to PIN verification
        if ($isLocked && !$request->is('verify-pin') && !$request->is('api/*')) {
            if ($request->expectsJson()) {
                return response()->json(['locked' => true], 403);
            }
            return redirect()->route('pin.verify');
        }

        return $next($request);
    }
}
