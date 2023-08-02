<?php

namespace App\Http\Resources\Api\V1\Admin\ExpiredSubscription;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpiredSubscriptionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'count' => $this->count,
            'checked_at' => $this->checked_at,
            'type' => $this->type,
            'token' => $this->token,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
