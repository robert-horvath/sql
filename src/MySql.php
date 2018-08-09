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
    private $db;

    /** @var mysqli_stmt */
    private $stmt;

    public function __construct()
    {
        $this->db = $this->connect();
        $this->reportAllErrors();
    }

    public function __destruct()
    {
        $this->db->close();
    }

    public function ping(): bool
    {
        return $this->db->ping();
    }

    public function execute(): array
    {
        $this->stmt->execute();
        $arr = $this->fetchObject($this->stmt->get_result());
        $this->stmt->reset();
        return $arr;
    }

    public function prepareWithParam(string $query, string $types = NULL, &...$vars): void
    {
        $this->stmt = $this->db->prepare($query);
        if ($types !== NULL)
            $this->stmt->bind_param($types, ...$vars);
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
}