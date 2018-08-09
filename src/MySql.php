<?php
declare(strict_types = 1);
namespace RHo\Sql;

use mysqli as _mysqli;
use mysqli_stmt;
use mysqli_driver;
use mysqli_result;

final class MySql implements SqlInterface
{

    /** @var _mysqli */
    private static $db = NULL;

    /** @var mysqli_stmt */
    private $stmt;

    public function __construct(string $query, string $types = NULL, &...$vars)
    {
        $this->init();
        $this->stmt = $this->prepareAndBindParam($query, $types, ...$vars);
    }

    public static function disconnect(): bool
    {
        $retVal = self::$db->close();
        self::$db = NULL;
        return $retVal;
    }

    public function ping(): bool
    {
        return self::$db->ping();
    }

    public function execute(): array
    {
        $this->stmt->execute();
        $arr = $this->fetchObject($this->stmt->get_result());
        $this->stmt->reset();
        return $arr;
    }

    private function init(): void
    {
        if (self::$db === NULL) {
            self::$db = $this->connect();
            $this->reportAllErrors();
        }
    }

    private function fetchObject(mysqli_result $result): array
    {
        $arr = [];
        while ($row = $result->fetch_object())
            $arr[] = $row;
        $result->free();
        return $arr;
    }

    private function connect(): _mysqli
    {
        $db = new _mysqli();
        $db->real_connect();
        return $db;
    }

    private function reportAllErrors(): void
    {
        $driver = new mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ALL;
    }

    private function prepareAndBindParam(string $query, ?string $types, &...$vars): mysqli_stmt
    {
        $stmt = self::$db->prepare($query);
        if ($types !== NULL)
            $stmt->bind_param($types, ...$vars);
        return $stmt;
    }
}