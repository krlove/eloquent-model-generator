<?php

namespace Krlove\EloquentModelGenerator;

use Illuminate\Database\DatabaseManager;

class TypeRegistry
{
    protected array $types = [
        'array'        => 'array',
        'simple_array' => 'array',
        'json_array'   => 'string',
        'bigint'       => 'integer',
        'boolean'      => 'boolean',
        'datetime'     => 'string',
        'datetimetz'   => 'string',
        'date'         => 'string',
        'time'         => 'string',
        'decimal'      => 'float',
        'integer'      => 'integer',
        'object'       => 'object',
        'smallint'     => 'integer',
        'string'       => 'string',
        'text'         => 'string',
        'binary'       => 'string',
        'blob'         => 'string',
        'float'        => 'float',
        'guid'         => 'string',
        'enum'         => 'string',
    ];

    public function __construct(private DatabaseManager $databaseManager) {}

    public function registerType(string $type, string $value, string $connection = null): void
    {
        $this->types[$type] = $value;

        $manager = $this->databaseManager->connection($connection)->getDoctrineSchemaManager();
        $manager->getDatabasePlatform()->registerDoctrineTypeMapping($type, $value);
    }

    public function resolveType(string $type): string
    {
        return array_key_exists($type, $this->types) ? $this->types[$type] : 'mixed';
    }
}
