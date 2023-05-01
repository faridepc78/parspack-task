<?php

namespace App\Repositories\Platform;

use App\Repositories\Contracts\BaseRepositoryInterface;

interface PlatformRepositoryInterface extends BaseRepositoryInterface
{
    public function getRandomByNames(array $names);
}
