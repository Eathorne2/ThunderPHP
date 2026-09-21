<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class PhpCodeCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $code = (string) ($node['data']['code'] ?? '');

        // The fullscreen editor highlights PHP more reliably when an opening tag is
        // present. Generated flow files are already inside PHP, so remove one tag
        // only when it is the first non-whitespace token supplied by the user.
        $code = (string) preg_replace('/^\s*<\?php\b\s*/i', '', $code, 1);

        // Also tolerate a conventional closing tag when it is the final token.
        $code = (string) preg_replace('/\s*\?>\s*$/', '', $code, 1);

        return [
            'code' => trim($code),
            'next_port' => 'exec',
        ];
    }
}
