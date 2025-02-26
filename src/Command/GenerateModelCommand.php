<?php

namespace Krlove\EloquentModelGenerator\Command;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;
use Krlove\EloquentModelGenerator\Generator;
use Krlove\EloquentModelGenerator\Helper\Prefix;
use Symfony\Component\Console\Input\InputArgument;

class GenerateModelCommand extends Command
{
    use GenerateCommandTrait;

    protected $name = 'krlove:generate:model';

    public function handle()
    {
        $generator       = $this->resolve(Generator::class);
        $databaseManager = $this->resolve(DatabaseManager::class);

        $config = $this->createConfig();
        $config->setClassName($this->argument('class-name'));
        Prefix::setPrefix($databaseManager->connection($config->getConnection())->getTablePrefix());

        $model = $generator->generateModel($config);
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
        return $this->getCommonOptions();
    }
}
