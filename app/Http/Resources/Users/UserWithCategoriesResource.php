<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Engines\MambuEngine;

class UserWithCategoriesResource extends JsonResource
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
            'balance'           => $balance,
            'photo'             => $this->photo,
            'ext_account_id'    => $this->ext_account_id,
            'created_at'        => (string)$this->created_at,
            'updated_at'        => (string)$this->updated_at,
            'categories'        => $this->userCategories->transform(function ($userCategory) {
                return [
                    'category_id'       => $userCategory->category->id,
                    'name'              => $userCategory->category->name,
                    'level'             => $userCategory->level,
                    'experience'        => $userCategory->experience,
                    'total_experience'  => $userCategory->total_experience
                ];
            }),
        ];
    }
}
