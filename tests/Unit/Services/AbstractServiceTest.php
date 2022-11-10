<?php

namespace Tests\Unit\Services;

use App\Abstraction\ModelSaverInterface;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class AbstractServiceTest extends TestCase
{
    protected MockObject|Model $model;

    protected function getParams(): ModelSaverInterface|MockObject
    {
        $modelSaver = $this->createMock(ModelSaverInterface::class);
        $this->model = $this->mockModel();
        $callback = function (string $class): Model {
            return $this->createModel();
        };
        $modelSaver->expects($this->any())
            ->method('create')
            ->willReturnCallback($callback)
        ;

        return $modelSaver;
    }

    abstract protected function mockModel(): Model|MockObject;

    abstract protected function createModel(): Model;
}
