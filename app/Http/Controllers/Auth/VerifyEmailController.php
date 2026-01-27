<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->hasVerifiedEmail()) {
            if ($user->role_id == 3) {
                return redirect()->intended("/" . '?verified=1');
            }
            if ($user->role_id == 4) {
                return redirect()->route('portal.dashboard')->with('verified', true);
            }
            return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        
        if ($user->role_id == 3) {
            return redirect()->intended("/" . '?verified=1');
        }
        if ($user->role_id == 4) {
            return redirect()->route('portal.dashboard')->with('verified', true);
        }
        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }
}
