<?php

namespace Model;

class RawExpression
{
    public function __construct(public string $sql, public array $bindings = []) {}
}