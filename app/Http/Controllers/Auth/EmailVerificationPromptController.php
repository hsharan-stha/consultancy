<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            $user = $request->user();
            if ($user->role_id == 3) {
                return redirect("/");
            }
            if ($user->role_id == 4) {
                return redirect()->route('portal.dashboard');
            }
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        return view('auth.verify-email');
    }
}
