<?php
namespace RHo\Sql;

interface SqlInterface
{

    public function __construct(string $query, string $types = NULL, &...$vars);

    public static function disconnect(): bool;

    public function ping(): bool;

    public function execute(): array;
}