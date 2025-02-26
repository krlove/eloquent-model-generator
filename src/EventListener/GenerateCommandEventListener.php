<?php

namespace Krlove\EloquentModelGenerator\EventListener;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Container\Container;
use Krlove\EloquentModelGenerator\TypeRegistry;

class GenerateCommandEventListener
{
    private const SUPPORTED_COMMANDS = [
        'krlove:generate:model',
        'krlove:generate:models',
    ];

    public function handle(CommandStarting $event): void
    {
        if (! in_array($event->command, self::SUPPORTED_COMMANDS)) {
            return;
        }

        /** @var TypeRegistry $typeRegistry */
        $typeRegistry = Container::getInstance()->make(TypeRegistry::class);

        $userTypes = config('eloquent_model_generator.db_types', []);
        foreach ($userTypes as $type => $value) {
            $typeRegistry->registerType($type, $value);
        }
    }
}
