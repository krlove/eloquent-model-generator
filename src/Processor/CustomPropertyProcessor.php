<?php

namespace Krlove\EloquentModelGenerator\Processor;

use Krlove\CodeGenerator\Model\DocBlockModel;
use Krlove\CodeGenerator\Model\PropertyModel;
use Krlove\EloquentModelGenerator\Config\Config;
use Krlove\EloquentModelGenerator\Model\EloquentModel;

class CustomPropertyProcessor implements ProcessorInterface
{
    public function process(EloquentModel $model, Config $config): void
    {
        if ($config->getNoTimestamps()) {
            $pNoTimestamps = new PropertyModel('timestamps', 'public', false);
            $pNoTimestamps->setDocBlock(
                new DocBlockModel('Indicates if the model should be timestamped.', '', '@var bool')
            );
            $model->addProperty($pNoTimestamps);
        }

        if ($config->getDateFormat() !== null) {
            $pDateFormat = new PropertyModel('dateFormat', 'protected', $config->getDateFormat());
            $pDateFormat->setDocBlock(
                new DocBlockModel('The storage format of the model\'s date columns.', '', '@var string')
            );
            $model->addProperty($pDateFormat);
        }

        if ($config->getConnection()) {
            $pConnection = new PropertyModel('connection', 'protected', $config->getConnection());
            $pConnection->setDocBlock(
                new DocBlockModel('The connection name for the model.', '', '@var string')
            );
            $model->addProperty($pConnection);
        }
    }

    public function getPriority(): int
    {
        return 5;
    }
}
