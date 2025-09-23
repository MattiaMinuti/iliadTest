<?php

namespace App\Services;

use App\Dao\BaseDao;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseService
{
    protected $dao;

    public function __construct(BaseDao $dao)
    {
        $this->dao = $dao;
    }

    /**
     * Get all records
     */
    public function getAll(): Collection
    {
        return $this->dao->getAll();
    }

    /**
     * Find by ID
     */
    public function findById(int $id): ?Model
    {
        return $this->dao->findById($id);
    }

    /**
     * Find by ID or fail
     */
    public function findByIdOrFail(int $id): Model
    {
        return $this->dao->findByIdOrFail($id);
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        return $this->dao->create($data);
    }

    /**
     * Update record
     */
    public function update(Model $model, array $data): bool
    {
        return $this->dao->update($model, $data);
    }

    /**
     * Delete record
     */
    public function delete(Model $model): bool
    {
        return $this->dao->delete($model);
    }

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->dao->paginate($perPage);
    }
}
