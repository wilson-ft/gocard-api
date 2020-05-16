<?php

namespace App\Http\Resources\UserCategories;

use Illuminate\Http\Resources\Json\JsonResource;

class UserCategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'category_id'       => $this->category_id,
            'level'             => $this->level,
            'experience'        => $this->experience,
            'total_experience'  => $this->total_experience,
            'created_at'        => (string)$this->created_at,
            'updated_at'        => (string)$this->updated_at
        ];
    }
}
