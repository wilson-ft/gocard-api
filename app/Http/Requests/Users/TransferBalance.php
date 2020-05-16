<?php

namespace App\Http\Requests\Users;

use App\Http\Requests\BaseAPIRequest as FormRequest;

class TransferBalance extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'type'      => ['required', 'in:deposit,transfer'],
            'amount'    => ['required', 'numeric']
        ];
    }
}
