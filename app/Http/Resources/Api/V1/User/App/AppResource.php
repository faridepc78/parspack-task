<?php

namespace App\Http\Resources\Api\V1\User\App;

use App\Http\Resources\Api\V1\User\Platform\PlatformResource;
use App\Http\Resources\Api\V1\User\Subscription\SubscriptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AppResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'platform_id' => $this->platform_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'platform' => $this->whenLoaded('platform', function () {
                return PlatformResource::make($this->platform);
            }),
            'subscription' => $this->whenLoaded('subscription', function () {
                return SubscriptionResource::make($this->subscription);
            }),
        ];
    }
}
