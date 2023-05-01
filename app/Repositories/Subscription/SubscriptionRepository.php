<?php

namespace App\Repositories\Subscription;

use App\Models\Subscription;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public Model $model;

    public function __construct(Subscription $model)
    {
        $this->model = $model;
    }
}
