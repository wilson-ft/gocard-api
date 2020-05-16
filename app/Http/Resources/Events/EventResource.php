<?php

namespace App\Http\Resources\Events;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'category_id'   => $this->category_id,
            'name'          => $this->name,
            'address'       => $this->address,
            'price'         => $this->price,
            'experience'    => $this->experience,
            'label'         => $this->label,
            'cashback'      => $this->cashback,
            'located_at'    => $this->located_at,
            'open_at'       => (string)$this->open_at,
            'closed_at'     => (string)$this->closed_at,
            'created_at'    => (string)$this->created_at,
            'updated_at'    => (string)$this->updated_at,
            'category'      => [
                'id'    => $this->category->id,
                'name'  => $this->category->name
            ]
        ];
    }
}
