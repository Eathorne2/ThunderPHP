<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Lifecycle;

require_once dirname(__DIR__) . '/View/Compiler.php';

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ReusableViewCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $path = trim(str_replace('\\', '/', (string) ($data['filename'] ?? '')), '/');
        if ($path === '') {
            $context->addError('Reusable View requires a partial path.');
            return [];
        }

        // Reuse the normal View file/component/theme compiler, but deliberately
        // discard all hook and route output. A Reusable View is only a partial.
        $result = (new ViewCompiler())->compile($node, $context);

        return [
            'files' => is_array($result['files'] ?? null) ? $result['files'] : [],
        ];
    }
}
