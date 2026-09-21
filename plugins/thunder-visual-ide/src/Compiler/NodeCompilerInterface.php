<?php

declare(strict_types=1);

namespace ThunderVisualIde\Compiler;

interface NodeCompilerInterface
{
    /**
     * @param array<string,mixed> $node
     * @return array<string,mixed>
     */
    public function compile(array $node, CompileContext $context): array;
}
