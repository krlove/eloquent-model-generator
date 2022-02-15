<?php

namespace Krlove\EloquentModelGenerator\Command;

use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

trait GenerateCommandTrait
{
    protected function saveModel(EloquentModel $model): void
    {
        $content = $model->render();

        $outputFilepath = $this->resolveOutputPath() . '/' . $model->getName()->getName() . '.php';
        if (!$this->option('no-backup') && file_exists($outputFilepath)) {
            rename($outputFilepath, $outputFilepath . '~');
        }
        file_put_contents($outputFilepath, $content);
    }

    protected function resolveOutputPath(): string
    {
        $path = $this->option('output-path');
        if ($path === null) {
            $path = app()->path('Models');
        } elseif (!str_starts_with($path, '/')) {
            $path = app()->path($path);
        }

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new GeneratorException(sprintf('Could not create directory %s', $path));
            }
        }

        if (!is_writeable($path)) {
            throw new GeneratorException(sprintf('%s is not writeable', $path));
        }

        return $path;
    }
}