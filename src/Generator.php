<?php

namespace Krlove\Generator;

use Krlove\Generator\Exception\GeneratorException;
use Krlove\Generator\Model\Model;

/**
 * Class Generator
 * @package Krlove\Generator
 */
class Generator
{
    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * Generator constructor.
     * @param Builder  $builder
     * @param Renderer $renderer
     */
    public function __construct(Builder $builder, Renderer $renderer)
    {
        $this->builder  = $builder;
        $this->renderer = $renderer;
    }

    /**
     * @param Config $config
     * @return Model
     * @throws Exception\RendererException
     * @throws GeneratorException
     */
    public function generateModel(Config $config)
    {
        $this->validateConfig($config);
        $model = $this->builder->createModel($config);
        $content = $this->renderer->render($model, $config->get('template_path'));

        $outputPath = $this->resolveOutputPath($config->get('output_path'), $model);
        file_put_contents($outputPath, $content);

        return $model;
    }

    /**
     * @param Config $config
     * @throws GeneratorException
     */
    protected function validateConfig(Config $config)
    {
        if (!$config->has('table_name')) {
            throw new GeneratorException('Table name must be specified');
        }
        if (!$config->has('output_path')) {
            throw new GeneratorException('Output path must be specified');
        }
    }

    /**
     * @param string $dir
     * @param Model $model
     * @return string
     */
    protected function resolveOutputPath($dir, Model $model)
    {
        return $dir . '/' . $model->getClassName() . '.php';
    }
}
