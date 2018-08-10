<?php
namespace RHo\Sql;

interface SqlInterface
{

    public function ping(): bool;

    public function prepareWithParam(string $query, string $types = NULL, &...$vars): SqlInterface;

    public function execute(): array;
}