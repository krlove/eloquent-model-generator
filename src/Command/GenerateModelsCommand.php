<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Config\Repository as AppConfig;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Krlove\EloquentModelGenerator\Generator;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Symfony\Component\Console\Input\InputOption;

class GenerateModelsCommand extends Command
{
    use GenerateCommandTrait;

    protected $name = 'krlove:generate:models';

    public function __construct(private Generator $generator, private DatabaseManager $databaseManager)
    {
        parent::__construct();
    }

    public function handle()
    {
        $config = $this->createConfig();
        Prefix::setPrefix($this->databaseManager->connection($config->getConnection())->getTablePrefix());

        $schemaManager = $this->databaseManager->connection($config->getConnection())->getDoctrineSchemaManager();
        $tables = $schemaManager->listTables();
        $skipTables = $this->option('skip-table');
        foreach ($tables as $table) {
            $tableName = Prefix::remove($table->getName());
            if (in_array($tableName, $skipTables)) {
                continue;
            }

            $config->setClassName(EmgHelper::getClassNameByTableName($tableName));
            $model = $this->generator->generateModel($config);
            $this->saveModel($model);

            $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
        }
    }

    protected function getOptions()
    {
        return array_merge(
            $this->getCommonOptions(),
            [
                ['skip-table', 'sk', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Tables to skip generating models for', null],
            ],
        );
    }
}