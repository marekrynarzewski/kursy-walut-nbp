<?php

namespace App\Abstraction;

use Illuminate\Database\Eloquent\Model;

interface ModelSaverInterface
{
    public function save(Model $model);

    public function remove(Model $model);

    public function create(string $class): Model;
}
