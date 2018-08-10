<?php
declare(strict_types = 1);
namespace RHo\SqlTest;

use PHPUnit\Framework\TestCase;

class MySqlTest extends TestCase
{

    public function testStoredProcedure(): void
    {
        $db = new \RHo\Sql\MySql();

        $i = 9;
        $s = '!';
        $db->prepareWithParam('CALL `test_schema`.sp(?,?)', 'is', $i, $s);
        $this->assertTrue($db->ping());

        $this->assertEquals([
            (object) [
                'id' => 10,
                'value' => 'ten!'
            ],
            (object) [
                'id' => 20,
                'value' => 'twenty!'
            ],
            (object) [
                'id' => 30,
                'value' => 'thirty!'
            ]
        ], $db->execute());
        $this->assertTrue($db->ping());

        $i = 20;
        $s = '?';
        $this->assertEquals([
            (object) [
                'id' => 30,
                'value' => 'thirty?'
            ]
        ], $db->execute());
        $this->assertTrue($db->ping());
    }

    public function testStoredProcedures(): void
    {
        $db1 = new \RHo\Sql\MySql();
        $db2 = new \RHo\Sql\MySql();

        $i1 = 19;
        $s1 = '!';
        $i2 = 29;
        $s2 = '#';

        $db1->prepareWithParam('CALL `test_schema`.sp(?,?)', 'is', $i1, $s1);
        $db2->prepareWithParam('CALL `test_schema`.sp(?,?)', 'is', $i2, $s2);
        $this->assertTrue($db1->ping());
        $this->assertTrue($db2->ping());

        $this->assertEquals([
            (object) [
                'id' => 20,
                'value' => 'twenty!'
            ],
            (object) [
                'id' => 30,
                'value' => 'thirty!'
            ]
        ], $db1->execute());

        $this->assertEquals([
            (object) [
                'id' => 30,
                'value' => 'thirty#'
            ]
        ], $db2->execute());
    }

    public function testFunction1(): void
    {
        $db = new \RHo\Sql\MySql();
        $this->assertEquals([
            (object) [
                'x' => '100'
            ]
        ], $db->prepareWithParam('SELECT `test_schema`.f1() as `x`')
            ->execute());
        $this->assertTrue($db->ping());
    }

    public function testFunction2(): void
    {
        $i = 0;
        $db = new \RHo\Sql\MySql();
        $db->prepareWithParam('SELECT `test_schema`.f2(?) as `i`', 'i', $i);
        $this->assertEquals([
            (object) [
                'i' => 1
            ]
        ], $db->execute());
        $this->assertTrue($db->ping());

        $i = 100;
        $this->assertEquals([
            (object) [
                'i' => 101
            ]
        ], $db->execute());
    }

    public function testStoredProcAndFunction(): void
    {
        $db = new \RHo\Sql\MySql();

        $i = 19;
        $s = '%';

        $db->prepareWithParam('CALL `test_schema`.sp(?,?)', 'is', $i, $s);

        $this->assertEquals([
            (object) [
                'id' => 20,
                'value' => 'twenty%'
            ],
            (object) [
                'id' => 30,
                'value' => 'thirty%'
            ]
        ], $db->execute());

        $db->prepareWithParam('SELECT `test_schema`.f2(?) as `i`', 'i', $i);
        $this->assertEquals([
            (object) [
                'i' => 20
            ]
        ], $db->execute());

        $i = - 1;
        $this->assertEquals([
            (object) [
                'i' => 0
            ]
        ], $db->execute());
    }

    public function testConnectionErrorWithUnknownUser(): void
    {
        ini_set('mysqli.default_user', 'unknown');
        $this->expectException(\mysqli_sql_exception::class);
        $this->expectExceptionMessageRegExp("/^Access denied for user 'unknown'@'localhost' \(using password: YES\)$/");
        $this->expectExceptionCode(1045);
        new \RHo\Sql\MySql();
    }

    public function testConnectionErrorWithWrongPassword(): void
    {
        ini_set('mysqli.default_host', 'local');
        $this->expectException(\mysqli_sql_exception::class);
        $this->expectExceptionMessageRegExp("/^php_network_getaddresses: getaddrinfo failed: Name or service not known$/");
        $this->expectExceptionCode(2002);
        new \RHo\Sql\MySql('SET @a=1');
    }

    public function testPrepareError(): void
    {
        ini_set('mysqli.default_user', 'testuser');
        ini_set('mysqli.default_host', 'localhost');
        $this->expectException(\mysqli_sql_exception::class);
        $this->expectExceptionMessageRegExp("/^You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near '' at line 1$/");
        $this->expectExceptionCode(1064);
        $db = new \RHo\Sql\MySql();
        $db->prepareWithParam('CALL');
    }
}