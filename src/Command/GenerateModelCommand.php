<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Console\Command;
use Krlove\EloquentModelGenerator\Config;
use Krlove\EloquentModelGenerator\Generator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
     * GenerateModelCommand constructor.
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        parent::__construct();

        $this->generator = $generator;
    }

    /**
     * Executes the command
     */
    public function fire()
    {
        $config = $this->createConfig();

        $model = $this->generator->generateModel($config);

        $this->output->writeln(sprintf('Model %s generated', $model->getName()->getName()));
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
            $config[$option[0]] = $this->option($option[0]);
        }

        return new Config($config);
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['class-name', InputArgument::REQUIRED, 'Name of the table'],
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
            ['base-class-name', 'bc', InputOption::VALUE_OPTIONAL, 'Class that model must extend', null],
            ['config', 'c', InputOption::VALUE_OPTIONAL, 'Path to config file to use', null],
            ['no-timestamps', 'ts', InputOption::VALUE_NONE, 'Set timestamps property to false', null],
            ['date-format', 'df', InputOption::VALUE_OPTIONAL, 'dateFormat property', null],
            ['connection', 'cn', InputOption::VALUE_OPTIONAL, 'Connection property', null],
        ];
    }
}
