<?php
namespace RHo\Sql;

interface SqlInterface
{

    public function ping(): bool;

    public function prepareWithParam(string $query, string $types = NULL, &...$vars): void;

    public function execute(): array;
}