<?php
declare(strict_types = 1);
namespace RHo\SqlTest;

use PHPUnit\Framework\TestCase;

class MySqlTest extends TestCase
{

    public function testStoredProcedure(): void
    {
        $i = 9;
        $s = '!';
        $db = new \RHo\Sql\MySql('CALL `test_schema`.sp(?,?)', 'is', $i, $s);
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

    public function testFunction1(): void
    {
        $db = new \RHo\Sql\MySql('SELECT `test_schema`.f1() as `x`');
        $this->assertEquals([
            (object) [
                'x' => '100'
            ]
        ], $db->execute());
        $this->assertTrue($db->ping());
    }

    public function testFunction2(): void
    {
        $i = 0;
        $db = new \RHo\Sql\MySql('SELECT `test_schema`.f2(?) as `i`', 'i', $i);
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

    public function testDisconnect(): void
    {
        $db = new \RHo\Sql\MySql('SET @a=1');
        $this->assertTrue($db->ping());
        $this->assertTrue($db::disconnect());
    }

    public function testConnectionErrorWithUnknownUser(): void
    {
        ini_set('mysqli.default_user', 'unknown');
        $this->expectException(\mysqli_sql_exception::class);
        $this->expectExceptionMessageRegExp("/^Access denied for user 'unknown'@'localhost' \(using password: YES\)$/");
        $this->expectExceptionCode(1045);
        new \RHo\Sql\MySql('SET @a=1');
    }

    public function testConnectionErrorWithWrongHost(): void
    {
        ini_set('mysqli.default_host', '1.2.3.4.5');
        $this->expectException(\mysqli_sql_exception::class);
        $this->expectExceptionMessageRegExp("/^php_network_getaddresses: getaddrinfo failed: Name or service not known$/");
        $this->expectExceptionCode(2002);
        new \RHo\Sql\MySql('SET @a=1');
    }
}