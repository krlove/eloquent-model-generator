<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Config\Repository as AppConfig;
use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Generator;
use Symfony\Component\Console\Input\InputOption;

class GenerateModelsCommand extends Command
{
    use GenerateCommandTrait;

    protected $name = 'krlove:generate:models';

    public function __construct(
        private Generator $generator,
        private AppConfig $appConfig,
        private DatabaseManager $databaseManager
    ) {
        parent::__construct();
    }

    public function handle()
    {
        // todo create config
        $config = new Config();

        $model = $this->generator->generateModel($config);
        $this->saveModel($model);

        $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
    }

    // todo get common options
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
            ['no-backup', 'b', InputOption::VALUE_NONE, 'Backup existing model', null],
            ['skip-table', 'sk', InputOption::VALUE_IS_ARRAY, 'Tables to skip generating models for', null],
        ];
    }
}