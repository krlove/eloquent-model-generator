<?php

namespace Krlove\EloquentModelGenerator\Helper;

/**
 * Class RelationHelper
 * @package Krlove\EloquentModelGenerator\Helper
 */
class RelationHelper
{
    /**
     * @var string
     */
    public static $defaultPrimaryKey = 'id';

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
