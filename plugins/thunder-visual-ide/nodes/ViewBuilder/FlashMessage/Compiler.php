<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\ViewBuilder;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;
use ThunderVisualIde\Compiler\ViewNodeSupport;

final class FlashMessageCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'view')
        {
            return [];
        }

        $d = is_array($node['data'] ?? null) ? $node['data'] : [];
        $type = in_array((string)($d['message_type'] ?? 'success'), [
            'success',
            'fail',
        ], true) ? (string)$d['message_type'] : 'success';
        $erase = !array_key_exists('erase', $d) || !empty($d['erase']);
        $typeArg = var_export($type, true);
        $checkCall = 'message(' . $typeArg . ", '', false)";
        $displayExpression = $erase
            ? 'message(' . $typeArg . ", '', true)"
            : '$__tvi_message';
        $class = ViewNodeSupport::classes($d, 'thv-flash', 'thv-flash--' . $type);

        return [
            'html' => '<?php if ($__tvi_message = ' . $checkCall . '): ?><div' . ViewNodeSupport::attributes([
                'class' => $class,
                'style' => ViewNodeSupport::inlineStyle($d),
            ]) . '><?= esc(' . $displayExpression . ') ?></div><?php endif; ?>',
        ];
    }
}
