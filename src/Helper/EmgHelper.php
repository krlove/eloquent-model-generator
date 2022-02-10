<?php

namespace Krlove\EloquentModelGenerator\Helper;

use Illuminate\Support\Str;

class EmgHelper
{
    const DEFAULT_PRIMARY_KEY = 'id';

    public function getShortClassName(string $fullClassName): string
    {
        $pieces = explode('\\', $fullClassName);

        return end($pieces);
    }

    public function getDefaultTableName(string $className): string
    {
        return Str::plural(Str::snake($className));
    }

    public function getDefaultForeignColumnName(string $table): string
    {
        return sprintf('%s_%s', Str::singular($table), self::DEFAULT_PRIMARY_KEY);
    }

    public function getDefaultJoinTableName(string $tableOne, string $tableTwo): string
    {
        $tables = [Str::singular($tableOne), Str::singular($tableTwo)];
        sort($tables);

        return implode('_', $tables);
    }
}
