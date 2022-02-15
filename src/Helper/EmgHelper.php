<?php

namespace Krlove\EloquentModelGenerator\Helper;

use Illuminate\Support\Str;

class EmgHelper
{
    public const DEFAULT_PRIMARY_KEY = 'id';

    public static function getShortClassName(string $fullClassName): string
    {
        $pieces = explode('\\', $fullClassName);

        return end($pieces);
    }

    public static function getTableNameByClassName(string $className): string
    {
        return Str::plural(Str::snake($className));
    }

    public static function getClassNameByTableName(string $tableName): string
    {
        return Str::singular(Str::studly($tableName));
    }

    public static function getDefaultForeignColumnName(string $table): string
    {
        return sprintf('%s_%s', Str::singular($table), self::DEFAULT_PRIMARY_KEY);
    }

    public static function getDefaultJoinTableName(string $tableOne, string $tableTwo): string
    {
        $tables = [Str::singular($tableOne), Str::singular($tableTwo)];
        sort($tables);

        return implode('_', $tables);
    }
}
