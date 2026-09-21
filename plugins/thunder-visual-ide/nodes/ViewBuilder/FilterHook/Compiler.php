<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ViewFilterHookCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $hookName = trim((string) ($data['hook_name'] ?? ''));
        if ($hookName === '') {
            $context->addError('View Run Filter Hook requires a hook name.');
            return ['html' => '<!-- View filter hook name is missing -->'];
        }

        $argument = $context->inputExpression(
            (string) ($node['id'] ?? ''),
            'data',
            "''"
        );
        $filterCall = 'do_filter('
            . $context->export($hookName)
            . ', '
            . $argument
            . ')';
        $output = !empty($data['escape_output'])
            ? '<?= esc(' . $filterCall . ') ?>'
            : '<?= ' . $filterCall . ' ?>';

        return [
            'html' => $output,
            'component_name' => ViewNodeSupport::componentName(
                (string) ($data['component_name'] ?? '')
            ),
        ];
    }
}
