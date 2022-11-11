<?php

namespace App\Services;

use App\Abstraction\ModelSaverInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractService
{
    public function __construct(
        protected ModelSaverInterface $modelSaver,
        protected Model $model,
    ) {
    }

    protected function save(Model $model): void
    {
        $this->modelSaver->save($model);
    }

    protected function remove(Model $model): void
    {
        $this->modelSaver->remove($model);
    }

    protected function createModel(): Model
    {
        return new $this->model;
    }
}
