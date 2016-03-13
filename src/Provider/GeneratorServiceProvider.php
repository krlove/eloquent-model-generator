<?php

namespace Krlove\EloquentModelGenerator\Provider;

use Illuminate\Support\ServiceProvider;
use Krlove\EloquentModelGenerator\Command\GenerateModelCommand;

/**
 * Class GeneratorServiceProvider
 * @package Krlove\EloquentModelGenerator\Provider
 */
class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->commands([
            GenerateModelCommand::class,
        ]);
    }
}