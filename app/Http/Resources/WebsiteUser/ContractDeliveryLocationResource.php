<?php

namespace App\Http\Resources\WebsiteUser;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ContractDeliveryLocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'priority' => $this->priority,
            'notes' => $this->notes,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),

            // العلاقات
            'saved_location' => new SavedLocationResource($this->whenLoaded('savedLocation')),
            'contract' => new ContractResource($this->whenLoaded('contract')),
        ];
    }
}
