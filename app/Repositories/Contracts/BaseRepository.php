<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    // model property on class instances
    protected Model $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model by paginate
    public function paginate(int $per_page, ?array $relations = null): LengthAwarePaginator
    {
        return $this->model::query()
            ->when($relations, function ($query) use ($relations) {
                return $query->with($relations);
            })
            ->paginate($per_page);
    }

    // Get all instances of model
    public function all(?array $relations = null): Collection|array
    {
        $query = $this->model::query()->when(!empty($relations), function ($query) use ($relations) {
            return $query->with($relations);
        });

        return $query->get();
    }

    // create a new record in the database
    public function create(array $data): Model|Builder
    {
        return $this->model::query()
            ->create($data);
    }

    // update record in the database
    public function update(array $data, $id): bool|int
    {
        $record = $this->show($id);

        return $record->update($data);
    }

    // remove record from the database
    public function delete(int $id): int
    {
        return $this->model->destroy($id);
    }

    // get count records
    public function getCount(): int
    {
        return $this->model::query()
            ->count();
    }

    // get random records
    public function getRandom(int $count = null)
    {
        return $this->model::query()->when($count, function ($query) use ($count) {
            return $query->inRandomOrder()->limit($count)->get();
        }, function ($query) {
            return $query->inRandomOrder()->first();
        });
    }

    // show the record with the given id
    public function show(
        int $id,
        bool $fail = false,
        $withTrashed = false,
        ?array $relations = null
    ): Model|Collection|Builder|null {
        $query = $this->model::query()->when($withTrashed, function ($query) {
            return $query->withTrashed();
        })->when(!empty($relations), function ($query) use ($relations) {
            return $query->with($relations);
        });

        return $fail ? $query->findOrFail($id) : $query->find($id);
    }

    // first the record with the given column
    public function firstByColumn(
        string $column,
        $value,
        $fail = false,
        $withTrashed = false,
        ?array $relations = null
    ): Model|Builder|null {
        $query = $this->model::query()->where($column, '=', $value)
            ->when($withTrashed, function ($query) {
                return $query->withTrashed();
            })
            ->when(!empty($relations), function ($query) use ($relations) {
                return $query->with($relations);
            });

        return $fail ? $query->firstOrFail() : $query->first();
    }

    public function restore(int $id)
    {
        return tap($this->model::withTrashed()->findOrFail($id))->restore();
    }

    public function loadRelationsByModel(Model $model, array $relations): Model
    {
        return $model->load($relations);
    }
}
