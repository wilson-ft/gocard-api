<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseAPIRequest as FormRequest;

class StoreUser extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name'    => ['required'],
            'last_name'     => ['required'],
            'phone_no'      => ['required', 'unique:users,phone_no']
        ];
    }
}
