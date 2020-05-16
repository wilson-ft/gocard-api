<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Engines\MambuEngine;

class AuthResource extends JsonResource
{
    public function toArray($request)
    {
        $mambuEngine    = new MambuEngine;
        $getSaving      = $mambuEngine->getAccountSaving($this->ext_account_id);
        $balance        = $getSaving['data']->balance;

        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'phone_no'          => $this->phone_no,
            'balance'           => $this->balance,
            'photo'             => $this->photo,
            'ext_account_id'    => $this->ext_account_id,
            'api_token'         => $this->api_token,
            'created_at'        => (string)$this->created_at,
            'updated_at'        => (string)$this->updated_at
        ];
    }
}
