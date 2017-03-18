<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\DatabaseManager;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

/**
 * Class ExistenceCheckerProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class ExistenceCheckerProcessor implements ProcessorInterface
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * ExistenceCheckerProcessor constructor.
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @inheritdoc
     */
    public function process(EloquentModel $model, Config $config)
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        if (!$schemaManager->tablesExist($prefix . $model->getTableName())) {
            throw new GeneratorException(sprintf('Table %s does not exist', $prefix . $model->getTableName()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 8;
    }
}
