<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public Model $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
