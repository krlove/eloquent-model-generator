<?php

namespace Krlove\EloquentModelGenerator;

class TypeRegistry
{
    protected array $types = [
        'array'        => 'array',
        'bigint'       => 'integer',
        'binary'       => 'string',
        'blob'         => 'string',
        'boolean'      => 'boolean',
        'date'         => 'string',
        'datetime'     => 'string',
        'datetimetz'   => 'string',
        'decimal'      => 'float',
        'enum'         => 'string',
        'float'        => 'float',
        'guid'         => 'string',
        'integer'      => 'integer',
        'json_array'   => 'string',
        'object'       => 'object',
        'smallint'     => 'integer',
        'simple_array' => 'array',
        'string'       => 'string',
        'text'         => 'string',
        'time'         => 'string',
        'varchar'      => 'string',
    ];

    public function __construct()
    {
    }

    public function registerType(string $sqlType, string $phpType, string $connection = null): void
    {
        $this->types[$sqlType] = $phpType;
    }

    public function resolveType(string $type): string
    {
        if (array_key_exists($type, $this->types)) {
            return $this->types[$type];
        }

        $type = strtok($type, ' ');
        if (array_key_exists($type, $this->types)) {
            return $this->types[$type];
        }

        return 'mixed';
    }
}
