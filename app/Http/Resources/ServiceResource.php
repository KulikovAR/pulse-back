<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resource = $this->resource;
        return [
            'id' => is_array($resource) ? $resource['id'] : $this->id,
            'company_id' => is_array($resource) ? $resource['company_id'] : $this->company_id,
            'name' => is_array($resource) ? $resource['name'] : $this->name
        ];
    }
}