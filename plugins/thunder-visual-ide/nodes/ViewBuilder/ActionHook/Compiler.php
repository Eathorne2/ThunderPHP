<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class ViewActionHookCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $hookName = trim((string) ($data['hook_name'] ?? ''));
        if ($hookName === '') {
            $context->addError('View Run Action Hook requires a hook name.');
            return ['html' => '<!-- View action hook name is missing -->'];
        }

        $argument = $context->inputExpression(
            (string) ($node['id'] ?? ''),
            'data',
            '[]'
        );

        return [
            'html' => '<?php do_action('
                . $context->export($hookName)
                . ', '
                . $argument
                . '); ?>',
            'component_name' => ViewNodeSupport::componentName(
                (string) ($data['component_name'] ?? '')
            ),
        ];
    }
}
