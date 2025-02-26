<?php

namespace Krlove\EloquentModelGenerator\Helper;

use Illuminate\Support\Str;

class EmgHelper
{
    public const DEFAULT_PRIMARY_KEY = 'id';

    public static function getShortClassName(string $fqcn): string
    {
        $pieces = explode('\\', $fqcn);

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

    public static function getDefaultForeignColumnName(string $tableName): string
    {
        return sprintf('%s_%s', Str::singular($tableName), self::DEFAULT_PRIMARY_KEY);
    }

    public static function getDefaultJoinTableName(string $tableNameOne, string $tableNameTwo): string
    {
        $tables = [Str::singular($tableNameOne), Str::singular($tableNameTwo)];
        sort($tables);

        return implode('_', $tables);
    }

    public static function isColumnUniqueIndex(array $indexes, string $column): bool
    {
        foreach ($indexes as $index) {
            $indexColumns = $index['columns'];
            if (count($indexColumns) !== 1) {
                continue;
            }
            $indexColumn = $indexColumns[0];
            if ($indexColumn === $column && $index['unique']) {
                return true;
            }
        }

        return false;
    }
}
