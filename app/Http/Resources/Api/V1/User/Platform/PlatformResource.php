<?php

namespace App\Http\Resources\Api\V1\User\Platform;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'http_service_url' => $this->http_service_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
