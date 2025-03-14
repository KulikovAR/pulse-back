<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company_client' => $this->whenLoaded('companyClient', function() {
                return [
                    'custom_name' => $this->companyClient->first()?->name ?? null
                ];
            })
        ];
    }
}