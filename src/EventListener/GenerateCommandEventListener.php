<?php

namespace Krlove\EloquentModelGenerator\EventListener;

use Illuminate\Console\Events\CommandStarting;
use Krlove\EloquentModelGenerator\TypeRegistry;

class GenerateCommandEventListener
{
    private const SUPPORTED_COMMANDS = [
        'krlove:generate:model',
        'krlove:generate:models',
    ];

    public function __construct(private TypeRegistry $typeRegistry) {}

    public function handle(CommandStarting $event): void
    {
        if (!in_array($event->command, self::SUPPORTED_COMMANDS)) {
            return;
        }

        $userTypes = config('eloquent_model_generator.db_types', []);
        foreach ($userTypes as $type => $value) {
            $this->typeRegistry->registerType($type, $value);
        }
    }
}