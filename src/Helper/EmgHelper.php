<?php

namespace Krlove\EloquentModelGenerator\Helper;

use Illuminate\Support\Str;

/**
 * Class EmgHelper
 * @package Krlove\EloquentModelGenerator\Helper
 */
class EmgHelper
{
    /**
     * @var string
     */
    const DEFAULT_PRIMARY_KEY = 'id';

    /**
     * @param string $fullClassName
     * @return string
     */
    public function getShortClassName($fullClassName)
    {
        $pieces = explode('\\', $fullClassName);

        return end($pieces);
    }

    /**
     * @param string $className
     * @return string
     */
    public function getDefaultTableName($className)
    {
        return Str::plural(Str::snake($className));
    }

    /**
     * @param string $table
     * @return string
     */
    public function getDefaultForeignColumnName($table)
    {
        return sprintf('%s_%s', Str::singular($table), self::DEFAULT_PRIMARY_KEY);
    }

    /**
     * @param string $tableOne
     * @param string $tableTwo
     * @return string
     */
    public function getDefaultJoinTableName($tableOne, $tableTwo)
    {
        $tables = [Str::singular($tableOne), Str::singular($tableTwo)];
        sort($tables);

        return sprintf(implode('_', $tables));
    }
}
