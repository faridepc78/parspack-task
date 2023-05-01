<?php

namespace App\Repositories\Platform;

use App\Models\Platform;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class PlatformRepository extends BaseRepository implements PlatformRepositoryInterface
{
    public Model $model;

    public function __construct(Platform $model)
    {
        $this->model = $model;
    }

    public function getRandomByNames(array $names)
    {
        return $this->model::query()
            ->whereIn('name', $names)
            ->inRandomOrder()
            ->pluck('id')
            ->first();
    }
}
