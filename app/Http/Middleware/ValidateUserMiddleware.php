<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Session;

class ValidateUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = \Auth::user();

        if (!$user) {
            Session::flash('error', 'Your account is not activated.');
            return redirect('login');
        }

        if (!$user->is_email_verified) {
            \Auth::logout();
            Session::flash('error', 'Your account is not verified.');
            return redirect('login');
        }
        if (!$user->is_active) {
            \Auth::logout();
            Session::flash('error', 'Your account is deactivated. please contact your administrator.');
            return redirect('login');
        }

        if (!$user->set_password) {
            \Auth::logout();
            return response(view('auth.set_password', compact('user')));
        }

        return $next($request);
    }
}
