<?php

namespace Krlove\Generator;

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
     * @return string
     * @throws Exception\RendererException
     */
    public function generateModel(Config $config)
    {
        $model = $this->builder->createModel($config);

        $content = $this->renderer->render($model, $config->get('template_path'));

        echo $content;
        // todo implement saving to file
        //$outputPath = $this->resolveOutputPath($config->get('output_path'), $model);
        //file_put_contents($outputPath, $content);
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
