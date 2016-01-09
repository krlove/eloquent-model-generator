<?php

namespace Krlove\Generator;
use Krlove\Generator\Model\Model;
use Krlove\Generator\Model\Property;

/**
 * Class Renderer
 * @package Krlove\Generator
 */
class Renderer
{
    const KEY_NAMESPACE          = 'NAMESPACE';
    const KEY_CLASS_NAME         = 'CLASS_NAME';
    const KEY_VIRTUAL_PROPERTIES = 'VIRTUAL_PROPERTIES';
    const KEY_VIRTUAL_METHODS    = 'VIRTUAL_METHODS';
    const KEY_PROPERTIES         = 'PROPERTIES';
    const KEY_METHODS            = 'METHODS';

    /**
     * @var string
     */
    protected $defaultTemplatePath = 'template/model.template';

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
     * @param string|null $templatePath
     * @return string
     * @throws RendererException
     */
    public function render(Model $model, $templatePath = null)
    {
        if ($templatePath === null) {
            $templatePath = __DIR__ . '/' . $this->defaultTemplatePath;
        }

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
            ->processVirtualProperties()
            ->processVirtualMethods()
            ->processProperties();
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

        $output = '';
        /** @var Property $property */
        foreach ($properties as $property) {
            // todo add docblock for property
            $line = sprintf('    %s $%s', $property->getModifier(), $property->getName());
            if ($property->getValue() !== null) {
                $line .= sprintf(' = %s', $property->getValue());
            }
            $line .= ';';
            $output[] = $line;
        }
        $this->apply(static::KEY_PROPERTIES, implode(PHP_EOL, $output));

        return $this;
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
