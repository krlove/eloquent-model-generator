<?php

namespace unit\Helper;

use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Table;
use Illuminate\Database\Eloquent\Model;
use Krlove\EloquentModelGenerator\Helper\EmgHelper;
use PHPUnit\Framework\TestCase;

class EmgHelperTest extends TestCase
{
    /**
     * @dataProvider fqcnProvider
     */
    public function testGetShortClassName(string $fqcn, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getShortClassName($fqcn));
    }

    public function fqcnProvider(): array
    {
        return [
            ['fqcn' => Model::class, 'expected' => 'Model'],
            ['fqcn' => 'Custom\Name', 'expected' => 'Name'],
            ['fqcn' => 'ShortName', 'expected' => 'ShortName'],
        ];
    }

    /**
     * @dataProvider classNameProvider
     */
    public function testGetTableNameByClassName(string $className, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getTableNameByClassName($className));
    }

    public function classNameProvider(): array
    {
        return [
            ['className' => 'User', 'expected' => 'users'],
            ['className' => 'ServiceAccount', 'expected' => 'service_accounts'],
            ['className' => 'Mouse', 'expected' => 'mice'],
            ['className' => 'D', 'expected' => 'ds'],
        ];
    }

    /**
     * @dataProvider tableNameToClassNameProvider
     */
    public function testGetClassNameByTableName(string $tableName, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getClassNameByTableName($tableName));
    }

    public function tableNameToClassNameProvider(): array
    {
        return [
            ['className' => 'users', 'expected' => 'User'],
            ['className' => 'service_accounts', 'expected' => 'ServiceAccount'],
            ['className' => 'mice', 'expected' => 'Mouse'],
            ['className' => 'ds', 'expected' => 'D'],
        ];
    }

    /**
     * @dataProvider tableNameToForeignColumnNameProvider
     */
    public function testGetDefaultForeignColumnName(string $tableName, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getDefaultForeignColumnName($tableName));
    }

    public function tableNameToForeignColumnNameProvider(): array
    {
        return [
            ['tableName' => 'organizations', 'expected' => 'organization_id'],
            ['tableName' => 'service_accounts', 'expected' => 'service_account_id'],
            ['tableName' => 'mice', 'expected' => 'mouse_id'],
        ];
    }

    /**
     * @dataProvider tableNamesProvider
     */
    public function testGetDefaultJoinTableName(string $tableNameOne, string $tableNameTwo, string $expected): void
    {
        $this->assertEquals($expected, EmgHelper::getDefaultJoinTableName($tableNameOne, $tableNameTwo));
    }

    public function tableNamesProvider(): array
    {
        return [
            ['tableNameOne' => 'users', 'tableNameTwo' => 'roles', 'expected' => 'role_user'],
            ['tableNameOne' => 'roles', 'tableNameTwo' => 'users', 'expected' => 'role_user'],
            ['tableNameOne' => 'accounts', 'tableNameTwo' => 'profiles', 'expected' => 'account_profile'],
        ];
    }

    public function testIsColumnUnique(): void
    {
        $indexMock = $this->createMock(Index::class);
        $indexMock->expects($this->once())
            ->method('getColumns')
            ->willReturn(['column_0']);

        $indexMock->expects($this->once())
            ->method('isUnique')
            ->willReturn(true);

        $indexMocks = [$indexMock];

        $tableMock = $this->createMock(Table::class);
        $tableMock->expects($this->once())
            ->method('getIndexes')
            ->willReturn($indexMocks);

        $this->assertTrue(EmgHelper::isColumnUnique($tableMock, 'column_0'));
    }

    public function testIsColumnUniqueTwoIndexColumns(): void
    {
        $indexMock = $this->createMock(Index::class);
        $indexMock->expects($this->once())
            ->method('getColumns')
            ->willReturn(['column_0', 'column_1']);

        $indexMock->expects($this->never())
            ->method('isUnique');

        $indexMocks = [$indexMock];

        $tableMock = $this->createMock(Table::class);
        $tableMock->expects($this->once())
            ->method('getIndexes')
            ->willReturn($indexMocks);

        $this->assertFalse(EmgHelper::isColumnUnique($tableMock, 'column_0'));
    }

    public function testIsColumnUniqueIndexNotUnique(): void
    {
        $indexMock = $this->createMock(Index::class);
        $indexMock->expects($this->once())
            ->method('getColumns')
            ->willReturn(['column_0']);

        $indexMock->expects($this->once())
            ->method('isUnique')
            ->willReturn(false);

        $indexMocks = [$indexMock];

        $tableMock = $this->createMock(Table::class);
        $tableMock->expects($this->once())
            ->method('getIndexes')
            ->willReturn($indexMocks);

        $this->assertFalse(EmgHelper::isColumnUnique($tableMock, 'column_0'));
    }
}
