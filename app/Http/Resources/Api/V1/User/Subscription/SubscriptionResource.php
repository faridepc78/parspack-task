<?php

namespace App\Http\Resources\Api\V1\User\Subscription;

use App\Http\Resources\Api\V1\User\App\AppResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'app_id' => $this->app_id,
            'status' => $this->status,
            'expires_at' => $this->expires_at,
            'checked_at' => $this->checked_at,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'app' => $this->whenLoaded('app', function () {
                return AppResource::make($this->app);
            }),
        ];
    }
}
