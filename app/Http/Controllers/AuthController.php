<?php

namespace App\Http\Controllers;

use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use App\User;
use Crypt;
use Exception;
use Illuminate\Http\Request;
use Session;

class AuthController extends AppBaseController
{
    /** @var  AccountRepository */
    private $accountRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(AccountRepository $accountRepository, UserRepository $userRepository)
    {
        $this->accountRepository = $accountRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function verifyAccount()
    {
        $token = \Request::get('token', null);

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
     * @param  Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws Exception
     */
    public function setPassword(Request $request)
    {
        $input = $request->all();

        $error = $this->validateRules($input, User::$setPasswordRules);
        if (!empty($error)) {
            /** @var User $user */
            $user = User::findOrFail($input['user_id']);
            Session::flash('error', $error);
            return view('auth.set_password', compact('user'));
        }

        $this->userRepository->setUserPassword($input);

        return redirect('home');
    }
}
