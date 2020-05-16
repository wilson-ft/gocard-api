<?php

namespace App\Http\Resources\UserCategories;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCategoryPaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'category_id'       => $this->category_id,
            'level'             => $this->level,
            'event_experience'  => $this->event_experience,
            'experience'        => $this->experience,
            'total_experience'  => $this->total_experience,
            'grand_total'       => $this->grand_total,
            'created_at'        => (string)$this->created_at,
            'updated_at'        => (string)$this->updated_at
        ];
    }
}
