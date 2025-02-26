<?php

namespace unit\Helper;

use Illuminate\Database\Eloquent\Model;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EmgHelperTest extends TestCase
{
    #[DataProvider('fqcnProvider')]
    public function testGetShortClassName(string $fqcn, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getShortClassName($fqcn));
    }

    public static function fqcnProvider(): array
    {
        return [
            ['fqcn' => Model::class,  'expected' => 'Model'],
            ['fqcn' => 'Custom\Name', 'expected' => 'Name'],
            ['fqcn' => 'ShortName',   'expected' => 'ShortName'],
        ];
    }

    #[DataProvider('classNameProvider')]
    public function testGetTableNameByClassName(string $className, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getTableNameByClassName($className));
    }

    public static function classNameProvider(): array
    {
        return [
            ['className' => 'User',           'expected' => 'users'],
            ['className' => 'ServiceAccount', 'expected' => 'service_accounts'],
            ['className' => 'Mouse',          'expected' => 'mice'],
            ['className' => 'D',              'expected' => 'ds'],
        ];
    }

    #[DataProvider('tableNameToClassNameProvider')]
    public function testGetClassNameByTableName(string $tableName, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getClassNameByTableName($tableName));
    }

    public static function tableNameToClassNameProvider(): array
    {
        return [
            ['tableName' => 'users',            'expected' => 'User'],
            ['tableName' => 'service_accounts', 'expected' => 'ServiceAccount'],
            ['tableName' => 'mice',             'expected' => 'Mouse'],
            ['tableName' => 'ds',               'expected' => 'D'],
        ];
    }

    #[DataProvider('tableNameToForeignColumnNameProvider')]
    public function testGetDefaultForeignColumnName(string $tableName, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getDefaultForeignColumnName($tableName));
    }

    public static function tableNameToForeignColumnNameProvider(): array
    {
        return [
            ['tableName' => 'organizations',    'expected' => 'organization_id'],
            ['tableName' => 'service_accounts', 'expected' => 'service_account_id'],
            ['tableName' => 'mice',             'expected' => 'mouse_id'],
        ];
    }

    #[DataProvider('tableNamesProvider')]
    public function testGetDefaultJoinTableName(string $tableNameOne, string $tableNameTwo, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getDefaultJoinTableName($tableNameOne, $tableNameTwo));
    }

    public static function tableNamesProvider(): array
    {
        return [
            ['tableNameOne' => 'users',    'tableNameTwo' => 'roles',    'expected' => 'role_user'],
            ['tableNameOne' => 'roles',    'tableNameTwo' => 'users',    'expected' => 'role_user'],
            ['tableNameOne' => 'accounts', 'tableNameTwo' => 'profiles', 'expected' => 'account_profile'],
        ];
    }

    public function testIsColumnUnique(): void
    {
        $indexes = [
            [
                'name'    => null,
                'primary' => false,
                'type'    => null,
                'unique'  => true,

                'columns' => [
                    'column_0',
                ],
            ],
        ];

        $this->assertTrue(EmgHelper::isColumnUniqueIndex($indexes, 'column_0'));
    }

    public function testIsColumnUniqueTwoIndexColumns(): void
    {
        $indexes = [
            [
                'name'    => null,
                'primary' => false,
                'type'    => null,
                'unique'  => true,

                'columns' => [
                    'column_0',
                    'column_1',
                ],
            ],
        ];

        $this->assertFalse(EmgHelper::isColumnUniqueIndex($indexes, 'column_0'));
    }

    public function testIsColumnUniqueIndexNotUnique(): void
    {
        $indexes = [
            [
                'name'    => null,
                'primary' => true,
                'type'    => null,
                'unique'  => false,

                'columns' => [
                    'column_0',
                ],
            ],
        ];

        $this->assertFalse(EmgHelper::isColumnUniqueIndex($indexes, 'column_0'));
    }
}
