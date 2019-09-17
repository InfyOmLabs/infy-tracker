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

class CheckUserIsActivated
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
        if (!$user->is_active) {
            \Auth::logout();
            Session::flash('error', 'Your account has been deactivated. please contact your administrator.');
            if ($request->ajax()) {
                return JsonResponse::fromJsonString(ResponseUtil::makeError('Your account has been deactivated. please contact your administrator.'), Response::HTTP_UNAUTHORIZED);
            }

            return redirect('login');
        }

        return $next($request);
    }
}
