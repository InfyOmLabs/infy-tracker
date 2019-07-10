<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 *
 * User: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 6/15/2019
 * Time: 10:44 AM
 */

namespace App\Http\Requests;

use App\Models\ActivityType;
use Illuminate\Foundation\Http\FormRequest;

class CreateActivityTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array The given data was invalid.
     */
    public function rules()
    {
        return ActivityType::$rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return ActivityType::$messages;
    }
}
