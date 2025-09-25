<?php

namespace App\Dao;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseDao
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Find a model by ID.
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a model by ID or fail.
     */
    public function findByIdOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Get all models.
     */
    public function getAll(): Collection
    {
        return $this->model->all();
    }

    /**
     * Create a new model.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a model.
     */
    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    /**
     * Delete a model.
     */
    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    /**
     * Paginate models.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Get models with relationships.
     */
    public function getWithRelations(array $relations): Collection
    {
        return $this->model->with($relations)->get();
    }

    /**
     * Find models with conditions.
     */
    public function findBy(array $conditions): Collection
    {
        $query = $this->model->newQuery();

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->whereIn($field, $value);
            } else {
                $query->where($field, $value);
            }
        }

        return $query->get();
    }
}
