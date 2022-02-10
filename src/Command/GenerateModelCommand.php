<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Config\Repository as AppConfig;
use Illuminate\Console\Command;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Exception\GeneratorException;
use Krlove\EloquentModelGenerator\Generator;
use Krlove\EloquentModelGenerator\Model\EloquentModel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class GenerateModelCommand extends Command
{
    protected $name = 'krlove:generate:model';

    protected Generator $generator;
    protected AppConfig $appConfig;

    public function __construct(Generator $generator, AppConfig $appConfig)
    {
        parent::__construct();

        $this->generator = $generator;
        $this->appConfig = $appConfig;
    }

    public function handle()
    {
        $config = $this->createConfig();

        $model = $this->generator->generateModel($config);
        $this->saveModel($model, $config);

        $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
    }

    protected function createConfig(): Config
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

    protected function saveModel(EloquentModel $model, Config $config): void
    {
        $content = $model->render();

        $outputPath = $this->resolveOutputPath($config);
        if ($config->get('backup') && file_exists($outputPath)) {
            rename($outputPath, $outputPath . '~');
        }
        file_put_contents($outputPath, $content);
    }

    protected function resolveOutputPath(Config $config): string
    {
        $path = $config->get('output_path');
        if ($path === null || stripos($path, '/') !== 0) {
            if (function_exists('app_path')) {
                $path = app_path($path);
            } else {
                $path = app('path') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
            }
        }

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                throw new GeneratorException(sprintf('Could not create directory %s', $path));
            }
        }

        if (!is_writeable($path)) {
            throw new GeneratorException(sprintf('%s is not writeable', $path));
        }

        return $path . '/' . $config->get('class_name') . '.php';
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
            ['output-path', 'op', InputOption::VALUE_OPTIONAL, 'Directory to store generated model', null],
            ['namespace', 'ns', InputOption::VALUE_OPTIONAL, 'Namespace of the model', null],
            ['base-class-name', 'bc', InputOption::VALUE_OPTIONAL, 'Model parent class', null],
            ['no-timestamps', 'ts', InputOption::VALUE_NONE, 'Set timestamps property to false', null],
            ['date-format', 'df', InputOption::VALUE_OPTIONAL, 'dateFormat property', null],
            ['connection', 'cn', InputOption::VALUE_OPTIONAL, 'Connection property', null],
            ['backup', 'b', InputOption::VALUE_NONE, 'Backup existing model', null]
        ];
    }
}
