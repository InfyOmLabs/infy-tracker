<?php

namespace App\Repositories;

use Exception;
use Illuminate\Mail\Message;
use Mail;
use URL;

/**
 * Class AccountRepository.
 */
class AccountRepository
{
    /**
     * @param string $username
     * @param string $email
     * @param string $activateCode
     *
     * @throws Exception
     */
    public function sendConfirmEmail($username, $email, $activateCode)
    {
        $data['link'] = URL::to('/activate?token='.$activateCode);
        $data['username'] = $username;

        try {
            Mail::send(
                'auth.emails.account_verification',
                ['data' => $data],
                function (Message $message) use ($email) {
                    $message->subject('Activate your account');
                    $message->to($email);
                }
            );
        } catch (Exception $e) {
            throw new Exception('Account created, but unable to send email');
        }
    }
}
