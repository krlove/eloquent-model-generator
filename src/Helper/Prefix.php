<?php

namespace Krlove\EloquentModelGenerator\Helper;

class Prefix
{
    public static function add(?string $prefix, string $tableName): string
    {
        return $prefix . $tableName;
    }

    public static function remove(?string $prefix, string $tableName): string
    {
        $prefix = preg_quote($prefix, '/');

        return preg_replace("/^$prefix/", '', $tableName);
    }
}