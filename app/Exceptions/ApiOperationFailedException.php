<?php
/**
 * Created by PhpStorm.
 * User: Shailesh-InfyOm
 * Date: 9/10/2016
 * Time: 3:58 PM.
 */

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiOperationFailedException extends Exception
{
    public $data;

    /**
     * ApiOperationFailedException constructor.
     *
     * @param string    $message
     * @param int       $code
     * @param Exception $previous
     * @param $data
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, $data = null)
    {
        if ($code == 0) {
            $code = Response::HTTP_BAD_REQUEST;
        }
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }
}
