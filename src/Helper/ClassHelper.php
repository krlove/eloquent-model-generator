<?php

namespace Krlove\EloquentModelGenerator\Helper;

/**
 * Class ClassHelper
 * @package Krlove\EloquentModelGenerator\Helper
 */
class ClassHelper
{
    /**
     * @param string $fullClassName
     * @return string
     */
    public static function getShortClassName($fullClassName)
    {
        $pieces = explode('\\', $fullClassName);

        return end($pieces);
    }
}
