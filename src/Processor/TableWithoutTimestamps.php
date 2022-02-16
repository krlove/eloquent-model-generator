<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Illuminate\Database\DatabaseManager;
use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\CodeGenerator\Model\UseTraitModel;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

/**
 * Class CustomPrimaryKeyProcessor
 * @package Krlove\EloquentModelGenerator\Processor
 */
class TableWithoutTimestamps implements ProcessorInterface
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
        $prefix = $this->databaseManager->connection($config->get('connection'))->getTablePrefix();

        $tableDetails = $schemaManager->listTableDetails($prefix . $model->getTableName());

        if (isset($tableDetails->getColumns()['created_at']) || isset($tableDetails->getColumns()['updated_at'])) {
            return;
        }

        $pNoTimestamps = new PropertyModel('timestamps', 'public', false);
        $pNoTimestamps->setDocBlock(
            new DocBlockModel('Indicates if the model should be timestamped.', '', '@var bool')
        );
        $model->addProperty($pNoTimestamps);
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return 6;
    }
}
