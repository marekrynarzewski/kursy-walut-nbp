<?php

namespace App\Services;

use App\Abstraction\ModelSaverInterface;
use Illuminate\Database\Eloquent\Model;

class DefaultModelSaver implements ModelSaverInterface
{
    public function save(Model $model)
    {
        $model->save();
    }

    public function remove(Model $model)
    {
        $model->delete();
    }

    public function create(string $class): Model
    {
       return new $class();
    }
}
