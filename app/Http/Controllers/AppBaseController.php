<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;
use Validator;

/**
 * Class AppBaseController.
 */
class AppBaseController extends Controller
{
    /**
     * @param array|mixed $result
     * @param string      $message
     *
     * @return JsonResponse
     */
    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    /**
     * @param string $error
     * @param int    $code
     *
     * @return JsonResponse
     */
    public function sendError($error, $code = 500)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }

    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    public function sendSuccess($message)
    {
        return Response::json([
            'success' => true,
            'message' => $message,
        ], 200);
    }

    /**
     * @param array $request
     * @param array $rules
     * @param array $ruleMessage
     *
     * @return null|string|void
     */
    public function validateRules($request, $rules, $ruleMessage = [])
    {
        $validator = Validator::make($request, $rules, $ruleMessage);
        if ($validator->fails()) {
            return $validator->messages()->first();
        }
    }
}
