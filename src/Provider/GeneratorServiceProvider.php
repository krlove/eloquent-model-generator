<?php

namespace Krlove\EloquentModelGenerator\Provider;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Krlove\EloquentModelGenerator\Command\GenerateModelCommand;
use Krlove\EloquentModelGenerator\Command\GenerateModelsCommand;
use Krlove\EloquentModelGenerator\EventListener\GenerateCommandEventListener;
use Krlove\EloquentModelGenerator\Generator;
use Krlove\EloquentModelGenerator\Processor\CustomPrimaryKeyProcessor;
use Krlove\EloquentModelGenerator\Processor\CustomPropertyProcessor;
use Krlove\EloquentModelGenerator\Processor\FieldProcessor;
use Krlove\EloquentModelGenerator\Processor\NamespaceProcessor;
use Krlove\EloquentModelGenerator\Processor\RelationProcessor;
use Krlove\EloquentModelGenerator\Processor\TableNameProcessor;
use Krlove\EloquentModelGenerator\TypeRegistry;

class GeneratorServiceProvider extends ServiceProvider
{
    public const PROCESSOR_TAG = 'eloquent_model_generator.processor';

    public function register()
    {
        $this->commands([
            GenerateModelCommand::class,
            GenerateModelsCommand::class,
        ]);

        $this->app->singleton(TypeRegistry::class);
        $this->app->singleton(GenerateCommandEventListener::class);

        $this->app->tag([
            FieldProcessor::class,
            NamespaceProcessor::class,
            RelationProcessor::class,
            CustomPropertyProcessor::class,
            TableNameProcessor::class,
            CustomPrimaryKeyProcessor::class,
        ], self::PROCESSOR_TAG);

        $this->app->bind(Generator::class, function ($app) {
            return new Generator($app->tagged(self::PROCESSOR_TAG));
        });
    }

    public function boot()
    {
        Event::listen(CommandStarting::class, [GenerateCommandEventListener::class, 'handle']);
    }
}
