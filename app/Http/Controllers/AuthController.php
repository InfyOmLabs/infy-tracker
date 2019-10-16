<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Crypt;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;
use Session;

/**
 * Class AuthController.
 */
class AuthController extends AppBaseController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Redirector|View
     */
    public function verifyAccount(Request $request)
    {
        $token = $request->get('token', null);

        if (empty($token)) {
            Session::flash('error', 'token not found');

            return redirect('login');
        }

        try {
            $token = Crypt::decrypt($token);
            list($userId, $activationCode) = $result = explode('|', $token);

            if (count($result) < 2) {
                Session::flash('error', 'token not found');

                return redirect('login');
            }

            /** @var User $user */
            $user = User::whereActivationCode($activationCode)->findOrFail($userId);

            if (empty($user)) {
                Session::flash('msg', 'This account activation token is invalid');

                return redirect('login');
            }
            if ($user->is_email_verified) {
                Session::flash('success', 'Your account already activated. Please do a login');

                return redirect('login');
            }

            $user->is_email_verified = 1;
            $user->save();
            if ($user->set_password) {
                Session::flash('success', 'Your account is successfully activated. Please do a login');

                return redirect('login');
            }

            return view('auth.set_password', compact('user'));
        } catch (Exception $e) {
            Session::flash('msg', 'Something went wrong');

            return redirect('login');
        }
    }

    /**
     * @param Request        $request
     * @param UserRepository $userRepository
     *
     * @throws Exception
     *
     * @return RedirectResponse|Redirector
     */
    public function setPassword(Request $request, UserRepository $userRepository)
    {
        $input = $request->all();

        $error = $this->validateRules($input, User::$setPasswordRules);
        if (!empty($error)) {
            /** @var User $user */
            $user = User::findOrFail($input['user_id']);
            Session::flash('error', $error);

            return view('auth.set_password', compact('user'));
        }

        $userRepository->setUserPassword($input);

        return redirect('home');
    }
}
