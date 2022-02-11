<?php

namespace Krlove\EloquentModelGenerator\Config;

use Illuminate\Database\Eloquent\Model;

class ConfigBuilder
{
    public const KEY_CLASS_NAME = 'class_name';
    public const KEY_TABLE_NAME = 'table_name';
    public const KEY_NAMESPACE = 'namespace';
    public const KEY_BASE_CLASS_NAME = 'base_class_name';
    public const KEY_OUTPUT_PATH = 'output_path';
    public const KEY_NO_TIMESTAMPS = 'no_timestamps';
    public const KEY_DATE_FORMAT = 'date_format';
    public const KEY_CONNECTION = 'connection';
    public const KEY_BACKUP = 'backup';
    public const KEY_DB_TYPES = 'db_types';

    private const DEFAULTS = [
        self::KEY_CLASS_NAME => null,
        self::KEY_TABLE_NAME => null,
        self::KEY_NAMESPACE => 'App\Models',
        self::KEY_BASE_CLASS_NAME => Model::class,
        self::KEY_OUTPUT_PATH => null,
        self::KEY_NO_TIMESTAMPS => null,
        self::KEY_DATE_FORMAT => null,
        self::KEY_CONNECTION => null,
        self::KEY_BACKUP => true,
        self::KEY_DB_TYPES => [
            'enum' => 'string',
        ],
    ];

    private array $inputConfig;
    private array $appConfig;

    public function __construct(array $inputConfig, array $appConfig = [])
    {
        $this->inputConfig = $this->resolveKeys($inputConfig);
        $this->appConfig = $appConfig;
    }

    public function build(): Config
    {
        $merged = $this->merge($this->inputConfig, $this->merge($this->appConfig, self::DEFAULTS));

        return (new Config())
            ->setClassName($merged[self::KEY_CLASS_NAME])
            ->setTableName($merged[self::KEY_TABLE_NAME])
            ->setNamespace($merged[self::KEY_NAMESPACE])
            ->setBaseClassName($merged[self::KEY_BASE_CLASS_NAME])
            ->setOutputPath($merged[self::KEY_OUTPUT_PATH])
            ->setNoTimestamps($merged[self::KEY_NO_TIMESTAMPS])
            ->setDateFormat($merged[self::KEY_DATE_FORMAT])
            ->setConnection($merged[self::KEY_CONNECTION])
            ->setBackup($merged[self::KEY_BACKUP])
            ->setDbTypes($merged[self::KEY_DB_TYPES]);
    }

    private function merge(array $high, array $low): array
    {
        foreach ($high as $key => $value)
        {
            $low[$key] = $value;
        }

        return $low;
    }

    private function resolveKeys(array $array): array
    {
        $resolved = [];
        foreach ($array as $key => $value) {
            $resolvedKey = $this->resolveKey($key);
            $resolved[$resolvedKey] = $value;
        }

        return $resolved;
    }

    private function resolveKey(string $key): string
    {
        return str_replace('-', '_', strtolower($key));
    }
}