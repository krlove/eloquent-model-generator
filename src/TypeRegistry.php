<?php

namespace Krlove\EloquentModelGenerator;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Config\Repository as AppConfig;
use Illuminate\Database\DatabaseManager;

/**
 * Class TypeRegistry
 * @package Krlove\EloquentModelGenerator
 */
class TypeRegistry
{
    /**
     * @var array
     */
    protected $types = [
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
    ];

    /**
     * @var AbstractSchemaManager
     */
    protected $manager;

    /**
     * @var AppConfig
     */
    protected $appConfig;

    /**
     * TypeRegistry constructor.
     * @param DatabaseManager $databaseManager
     * @param AppConfig $appConfig
     */
    public function __construct(DatabaseManager $databaseManager, AppConfig $appConfig)
    {
        $this->manager   = $databaseManager->connection()->getDoctrineSchemaManager();
        $this->appConfig = $appConfig;

        $this->registerUserTypes();
    }

    /**
     * @param string $type
     * @param string $value
     */
    public function registerType($type, $value)
    {
        $this->types[$type] = $value;
        $this->manager->getDatabasePlatform()->registerDoctrineTypeMapping($type, $value);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function resolveType($type)
    {
        return array_key_exists($type, $this->types) ? $this->types[$type] : 'mixed';
    }

    /**
     * Register types defined in application config
     */
    protected function registerUserTypes()
    {
        $userTypes = $this->appConfig->get('eloquent_model_generator.db_types');
        if ($userTypes && is_array($userTypes)) {
            foreach ($userTypes as $type => $value) {
                $this->registerType($type, $value);
            }
        }
    }
}
