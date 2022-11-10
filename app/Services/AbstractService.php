<?php

namespace App\Services;

use App\Abstraction\ModelSaverInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractService
{
    protected Model $model;

    public function __construct(
        protected ModelSaverInterface $modelSaver,
        $model = null,
    ) {
        $this->model = $model;
    }

    protected function save(Model $model)
    {
        $this->modelSaver->save($model);
    }

    protected function remove(Model $model)
    {
        $this->modelSaver->remove($model);
    }

    protected function createModel(): Model
    {
        return new $this->model;
    }
}
