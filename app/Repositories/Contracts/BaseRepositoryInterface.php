<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function paginate(int $per_page, ?array $relations = null);

    public function all(?array $relations = null);

    public function create(array $data);

    public function update(array $data, $id);

    public function delete(int $id);

    public function getCount();

    public function getRandom(int $count = null);

    public function show(int $id, bool $fail = false, $withTrashed = false, ?array $relations = null);

    public function firstByColumn(string $column, $value, bool $fail = false, $withTrashed = false, ?array $relations = null);

    public function loadRelationsByModel(Model $model, array $relations);

    public function restore(int $id);
}
