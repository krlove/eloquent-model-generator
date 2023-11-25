<?php

namespace Krlove\EloquentModelGenerator\Helper;

class Prefix
{
    private static string $prefix = '';

    public static function setPrefix(string $prefix): void
    {
        self::$prefix = $prefix;
    }

    public static function add(string $tableName): string
    {
        return self::$prefix . $tableName;
    }

    public static function remove(string $tableName): string
    {
        $prefix = preg_quote(self::$prefix, '/');

        return preg_replace("/^$prefix/", '', $tableName);
    }
}