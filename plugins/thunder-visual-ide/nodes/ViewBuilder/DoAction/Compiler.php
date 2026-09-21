<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class DoActionCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $hookName = trim((string) ($data['hook_name'] ?? ''));
        if ($hookName === '') {
            $context->addError('Do Action Component requires a hook name.');
            return ['html' => '<!-- Do Action hook name is missing -->'];
        }

        $variableName = ViewNodeSupport::variableName(
            ltrim((string) ($data['argument_name'] ?? '$data'), '$'),
            'data'
        );
        $componentName = ViewNodeSupport::componentName(
            (string) ($data['component_name'] ?? 'custom-hook')
        ) ?: 'custom-hook';

        return [
            'html' => '<?php do_action('
                . $context->export($hookName)
                . ', $'
                . $variableName
                . ' ?? []); ?>',
            'component_name' => $componentName,
            'uses_base_css' => false,
        ];
    }
}
