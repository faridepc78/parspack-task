<?php

namespace App\Repositories\SubscriptionCount;

use App\Models\SubscriptionCount;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class SubscriptionCountRepository extends BaseRepository implements SubscriptionCountRepositoryInterface
{
    public Model $model;

    public function __construct(SubscriptionCount $model)
    {
        $this->model = $model;
    }
}
