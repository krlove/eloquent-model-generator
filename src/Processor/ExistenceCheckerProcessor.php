<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

class ExistenceCheckerProcessor implements ProcessorInterface
{
    protected DatabaseManager $databaseManager;

    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    public function process(EloquentModel $model, Config $config): void
    {
        $schemaManager = $this->databaseManager->connection($config->get('connection'))->getDoctrineSchemaManager();
        $prefix = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        if (!$schemaManager->tablesExist($prefix . $model->getTableName())) {
            throw new GeneratorException(sprintf('Table %s does not exist', $prefix . $model->getTableName()));
        }
    }

    public function getPriority(): int
    {
        return 8;
    }
}
