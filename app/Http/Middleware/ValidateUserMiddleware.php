<?php

namespace App\Http\Middleware;

use App\Models\User;
use Auth;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Utils\ResponseUtil;
use Session;

class ValidateUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            Session::flash('error', 'Your account is not activated.');
            if ($request->ajax()) {
                return JsonResponse::fromJsonString(ResponseUtil::makeError('Your account is not activated.'), Response::HTTP_UNAUTHORIZED);
            }

            return redirect('login');
        }

        if (!$user->is_email_verified) {
            Auth::logout();
            Session::flash('error', 'Your account is not verified.');
            if ($request->ajax()) {
                return JsonResponse::fromJsonString(ResponseUtil::makeError('Your account is not verified.'), Response::HTTP_UNAUTHORIZED);
            }

            return redirect('login');
        }
        if (!$user->is_active) {
            Auth::logout();
            Session::flash('error', 'Your account is deactivated. please contact your administrator.');
            if ($request->ajax()) {
                return JsonResponse::fromJsonString(ResponseUtil::makeError('Your account is deactivated. please contact your administrator.'), Response::HTTP_UNAUTHORIZED);
            }

            return redirect('login');
        }

        if (!$user->set_password) {
            Auth::logout();

            return response(view('auth.set_password', compact('user')));
        }

        return $next($request);
    }
}
