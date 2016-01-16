<?php

namespace Krlove\Generator;

use Illuminate\Support\Str;
use Krlove\Generator\Exception\RendererException;
use Krlove\Generator\Model\BelongsTo;
use Krlove\Generator\Model\BelongsToMany;
use Krlove\Generator\Model\HasMany;
use Krlove\Generator\Model\Model;
use Krlove\Generator\Model\Property;
use Krlove\Generator\Model\Relation;

/**
 * Class Renderer
 * @package Krlove\Generator
 */
class Renderer
{
    const KEY_NAMESPACE          = 'NAMESPACE';
    const KEY_CLASS_NAME         = 'CLASS_NAME';
    const KEY_BASE_CLASS_NAME    = 'BASE_CLASS_NAME';
    const KEY_VIRTUAL_PROPERTIES = 'VIRTUAL_PROPERTIES';
    const KEY_VIRTUAL_METHODS    = 'VIRTUAL_METHODS';
    const KEY_PROPERTIES         = 'PROPERTIES';
    const KEY_METHODS            = 'METHODS';

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected $output;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     * @param string $templatePath
     * @return string
     * @throws RendererException
     */
    public function render(Model $model, $templatePath)
    {
        if (!file_exists($templatePath)) {
            throw new RendererException('Template %s does not exist', $templatePath);
        }

        $this->template = file_get_contents($templatePath);
        $this->model    = $model;

        return $this->process()->output;
    }

    /**
     * @return $this
     */
    protected function process()
    {
        $this->output = $this->template;

        return $this->processNamespace()
            ->processClassName()
            ->processBaseClassName()
            ->processVirtualProperties()
            ->processVirtualMethods()
            ->processProperties()
            ->processRelations();
    }

    /**
     * @return $this
     */
    protected function processNamespace()
    {
        $this->apply(static::KEY_NAMESPACE, $this->model->getNamespace());

        return $this;
    }

    /**
     * @return $this
     */
    protected function processClassName()
    {
        $this->apply(static::KEY_CLASS_NAME, $this->model->getClassName());

        return $this;
    }

    /**
     * @return $this
     */
    protected function processBaseClassName()
    {
        $this->apply(static::KEY_BASE_CLASS_NAME, $this->model->getBaseClass());

        return $this;
    }

    /**
     * @return $this
     */
    protected function processVirtualProperties()
    {
        $properties = $this->model->getProperties()->filter(function (Property $property) {
            return $property->isVirtual();
        });

        $output = [];
        /** @var Property $property */
        foreach ($properties as $property) {
            $output[] = sprintf(' * @property %s %s', $property->getType(), $property->getName());
        }
        $this->apply(static::KEY_VIRTUAL_PROPERTIES, implode(PHP_EOL, $output));

        return $this;
    }

    /**
     * @return $this
     */
    protected function processVirtualMethods()
    {
        $this->apply(static::KEY_VIRTUAL_METHODS, '');

        return $this;
    }

    /**
     * @return $this
     */
    protected function processProperties()
    {
        $properties = $this->model->getProperties()->filter(function (Property $property) {
            return !$property->isVirtual();
        });

        $output = [];
        /** @var Property $property */
        foreach ($properties as $property) {
            // todo add docblock for property
            $line = sprintf('    %s $%s', $property->getModifier(), $property->getName());
            if ($property->getValue() !== null) {
                $line .= sprintf(' = %s', $this->processPropertyValue($property->getValue()));
            }
            $line .= ';';
            $output[] = $line;
        }
        $this->apply(static::KEY_PROPERTIES, implode(PHP_EOL, $output));

        return $this;
    }

    /**
     * @return $this
     */
    protected function processRelations()
    {
        $output = [];
        /** @var BelongsTo $relation */
        foreach ($this->model->getRelations() as $relation) {
            $output[] = $this->processRelation($relation);
        }
        $this->apply(static::KEY_METHODS, implode(PHP_EOL . PHP_EOL, $output));

        return $this;
    }

    /**
     * @param Relation $relation
     * @return string
     */
    protected function processRelation(Relation $relation)
    {
        $className    = Str::singular(Str::studly($relation->getTable()));
        $relationType = Str::camel(class_basename($relation));

        if ($relation instanceof BelongsTo) {
            $methodName = Str::singular($relation->getTable());
        } else {
            $methodName = Str::plural($relation->getTable());
        }

        $method = [];
        $method[] = sprintf('    public function %s()', $methodName);
        $method[] = '    {';
        $method[] = sprintf('        return $this->%s(\'%s\');', $relationType, $className);
        $method[] = '    }';

        return implode(PHP_EOL, $method);
    }

    /**
     * @param mixed $value
     * @return string|null
     */
    protected function processPropertyValue($value)
    {
        $type = gettype($value);

        switch ($type) {
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'int':
                return $value;
            case 'string':
                return sprintf('\'%s\'', addslashes($value));
            case 'array':
                array_walk($value, function (&$element) {
                    $element = sprintf('\'%s\'', addslashes($element));
                });

                return '[' . implode(', ', $value) . ']'; // TODO add support for multidimensional array
            default:
                return null;
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function apply($key, $value)
    {
        $this->output = str_replace(sprintf('$%s', $key), $value, $this->output);
    }
}
