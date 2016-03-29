<?php

namespace Krlove\EloquentModelGenerator\Helper;
use Illuminate\Support\Str;

/**
 * Class TitleHelper
 * @package Krlove\EloquentModelGenerator\Helper
 */
class TitleHelper
{
    /**
     * @var string
     */
    public static $defaultPrimaryKey = 'id';

    /**
     * @param string $className
     * @return string
     */
    public static function getDefaultTableName($className)
    {
        return Str::plural(Str::snake($className));
    }

    /**
     * @param string $table
     * @return string
     */
    public static function getDefaultForeignColumnName($table)
    {
        return sprintf('%s_%s', $table, static::$defaultPrimaryKey);
    }

    /**
     * @param string $tableOne
     * @param string $tableTwo
     * @return string
     */
    public static function getDefaultJoinTableName($tableOne, $tableTwo)
    {
        $tables = [$tableOne, $tableTwo];
        sort($tables);

        return sprintf(implode('_', $tables));
    }
}
