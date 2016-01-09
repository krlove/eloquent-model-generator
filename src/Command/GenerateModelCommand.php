<?php

namespace Krlove\Generator\Command;

use Illuminate\Console\Command;
use Krlove\Generator\Generator;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GenerateModelCommand
 * @package Krlove\Generator\Command
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
        // resolve config
        $config = ['table_name' => $this->argument('table-name')];

        $file = $this->generator->generateModel($config);

        $this->output->writeln($file);
    }

    /**
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['table-name', InputArgument::REQUIRED, 'Name of the table',],
        ];
    }
}
