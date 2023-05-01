<?php

namespace App\Repositories\App;

use App\Models\App;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class AppRepository extends BaseRepository implements AppRepositoryInterface
{
    public Model $model;

    public function __construct(App $model)
    {
        $this->model = $model;
    }
}
