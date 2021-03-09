<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Config\Repository as AppConfig;
use Illuminate\Console\Command;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str;
use DB;

/**
 * Class GenerateModelCommand
 * @package Krlove\EloquentModelGenerator\Command
 */
class GenerateModelCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'krlove:generate:model';

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var AppConfig
     */
    protected $appConfig;

    /**
     * GenerateModelCommand constructor.
     * @param Generator $generator
     * @param AppConfig $appConfig
     */
    public function __construct(Generator $generator, AppConfig $appConfig)
    {
        parent::__construct();

        $this->generator = $generator;
        $this->appConfig = $appConfig;
    }

    /**
     * Executes the command
     */
    public function fire()
    {
        $config = $this->createConfig();
       
        if ($this->argument('class-name') === NULL) {
            $tables = DB::connection()->getDoctrineSchemaManager()->listTables();
            $defaultSchema = DB::connection()->getConfig()['schema'] ?? "";
            foreach($tables as $table){
                $currentSchema = $table->getNamespaceName();
                $isManyToManyTable = FALSE;
                if ($this->option('schema') === NULL || 
                    ($this->option('schema') !== NULL && ($this->option('schema') === $currentSchema || ($currentSchema === NULL && $this->option('schema') === $defaultSchema)))
                ) {

                    //Check if the table is a many to many relationship (having its primary key composed of 2 foreign keys)
                    $primaryKey = $table->getPrimaryKey();
                    $foreignKeys = $table->getForeignKeys();
                    
                    if ($primaryKey !== NULL && count($foreignKeys) === 2) {
                        $primaryKeyColumnNames = $table->getPrimaryKeyColumns();
                        $foreignKeysColumnNames = [];
                        foreach ($foreignKeys as $foreignKey) {
                            $foreignKeysColumnNames = array_merge($foreignKeysColumnNames,$foreignKey->getColumns());
                        }
                        if (count(array_intersect($foreignKeysColumnNames,$primaryKeyColumnNames)) === 2) {
                            $isManyToManyTable = TRUE;
                        }
                    }

                    if (!$isManyToManyTable) {
                        $tableName = $table->getShortestName($currentSchema);
                        $tableNameToSingular = Str::singular($tableName);
                        
                        

                        //Does not generate a model for many to many tables
                        if ($tableNameToSingular !== $tableName) {
                            $modelName = ucfirst(Str::camel(Str::singular($tableNameToSingular)));
                            $config->set('class_name',$modelName);
                            $config->set('table_name',$tableName);
                            $config->setSchemaName($table->getNamespaceName() ?? "");
                            $config->setDefaultSchemaName($defaultSchema);
                            $model = $this->generator->generateModel($config);
                            $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
                        }
                    }
                }
            }
        } else {
            $model = $this->generator->generateModel($config);
            $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
        }
    }

    /**
     * Add support for Laravel 5.5
     */
    public function handle()
    {
        $this->fire();
    }

    /**
     * @return Config
     */
    protected function createConfig()
    {
        $config = [];

        foreach ($this->getArguments() as $argument) {
            $config[$argument[0]] = $this->argument($argument[0]);
        }
        foreach ($this->getOptions() as $option) {
            $value = $this->option($option[0]);
            if ($option[2] == InputOption::VALUE_NONE && $value === false) {
                $value = null;
            }
            $config[$option[0]] = $value;
        }
        
        $config['db_types'] = $this->appConfig->get('eloquent_model_generator.db_types');
        
        return new Config($config, $this->appConfig->get('eloquent_model_generator.model_defaults'));
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['class-name', InputArgument::OPTIONAL, 'Model class name'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['table-name', 'tn', InputOption::VALUE_OPTIONAL, 'Name of the table to use', null],
            ['output-path', 'op', InputOption::VALUE_OPTIONAL, 'Directory to store generated model', null],
            ['namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Namespace of the model', null],
            ['base-class-name', 'bc', InputOption::VALUE_OPTIONAL, 'Model parent class', null],
            ['no-timestamps', 'ts', InputOption::VALUE_NONE, 'Set timestamps property to false', null],
            ['date-format', 'df', InputOption::VALUE_OPTIONAL, 'dateFormat property', null],
            ['connection', 'cn', InputOption::VALUE_OPTIONAL, 'Connection property', null],
            ['backup', 'b', InputOption::VALUE_NONE, 'Backup existing model', null],
            ['force-table-name', 'ftn', InputOption::VALUE_NONE, 'Force tableName property to be always set', null],
            ['schema', 's', InputOption::VALUE_OPTIONAL, 'Name of the database schema to generates models from, only used when class-name argument is not provided', null],
            ['no-class-phpdoc-block','ncpb', InputOption::VALUE_OPTIONAL, 'Does not generate the class php doc', null]
        ];
    }
}
