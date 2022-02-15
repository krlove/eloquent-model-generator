<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Config\Repository as AppConfig;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GenerateModelCommand extends Command
{
    use GenerateCommandTrait;

    protected $name = 'krlove:generate:model';

    public function __construct(private Generator $generator, private AppConfig $appConfig)
    {
        parent::__construct();
    }

    public function handle()
    {
        $config = (new Config())
            ->setClassName($this->argument('class-name'))
            ->setTableName($this->option('table-name'))
            ->setNamespace($this->option('namespace'))
            ->setBaseClassName($this->option('base-class-name'))
            ->setNoTimestamps($this->option('no-timestamps'))
            ->setDateFormat($this->option('date-format'))
            ->setConnection($this->option('connection'));

        $model = $this->generator->generateModel($config);
        $this->saveModel($model);

        $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
    }

    protected function getArguments()
    {
        return [
            ['class-name', InputArgument::REQUIRED, 'Model class name'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['table-name', 'tn', InputOption::VALUE_OPTIONAL, 'Name of the table to use', null],
            ['output-path', 'op', InputOption::VALUE_OPTIONAL, 'Directory to store generated model', config('eloquent_model_generator.output_path')],
            ['namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Namespace of the model', config('eloquent_model_generator.namespace', 'App\Models')],
            ['base-class-name', 'bc', InputOption::VALUE_OPTIONAL, 'Model parent class', config('eloquent_model_generator.base_class_name', Model::class)],
            ['no-timestamps', 'ts', InputOption::VALUE_OPTIONAL, 'Set timestamps property to false', config('eloquent_model_generator.no_timestamps', false)],
            ['date-format', 'df', InputOption::VALUE_OPTIONAL, 'dateFormat property', config('eloquent_model_generator.date_format')],
            ['connection', 'cn', InputOption::VALUE_OPTIONAL, 'Connection property', config('eloquent_model_generator.connection')],
            ['no-backup', 'b', InputOption::VALUE_OPTIONAL, 'Backup existing model', config('eloquent_model_generator.no_backup', false)],
        ];
    }
}
