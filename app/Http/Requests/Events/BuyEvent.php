<?php

namespace App\Http\Requests\Events;

use App\Http\Requests\BaseAPIRequest as FormRequest;

class BuyEvent extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'event_id' => ['required', 'exists:events,id']
        ];
    }
}
