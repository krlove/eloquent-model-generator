<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

class ExistenceCheckerProcessor implements ProcessorInterface
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function process(EloquentModel $model, Config $config): void
    {
        $schemaManager = $this->databaseManager->connection($config->getConnection())->getDoctrineSchemaManager();
        $prefix = $this->databaseManager->connection($config->getConnection())->getTablePrefix();

        $tableName = Prefix::add($prefix, $model->getTableName());
        if (!$schemaManager->tablesExist($tableName)) {
            throw new GeneratorException(sprintf('Table %s does not exist', $tableName));
        }
    }

    public function getPriority(): int
    {
        return 8;
    }
}
