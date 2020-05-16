<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseAPIRequest as FormRequest;

class VerifyUser extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone_no'  => ['required', 'exists:users,phone_no'],
            'photo'     => ['required']
        ];
    }
}
