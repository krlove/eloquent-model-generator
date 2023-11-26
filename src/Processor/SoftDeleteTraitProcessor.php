<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Krlove\CodeGenerator\Model\UseTraitModel;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

/**
 * Class CustomPrimaryKeyProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class SoftDeleteTraitProcessor implements ProcessorInterface
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * @var TypeRegistry
     */
    protected $typeRegistry;

    /**
     * FieldProcessor constructor.
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
        $prefix        = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        $tableDetails = $schemaManager->listTableDetails($prefix . $model->getTableName());

        if (!isset($tableDetails->getColumns()['deleted_at'])) {
            return;
        }

        $softDeleteTrait = new UseTraitModel('\Illuminate\Database\Eloquent\SoftDeletes');
        $model->addTrait($softDeleteTrait);
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 6;
    }
}
