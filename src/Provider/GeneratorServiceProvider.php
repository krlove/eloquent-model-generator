<?php

namespace Krlove\Generator\Provider;

use Illuminate\Support\ServiceProvider;
use Krlove\Generator\Command\GenerateModelCommand;

/**
 * Class GeneratorServiceProvider
 * @package Krlove\Generator\Provider
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