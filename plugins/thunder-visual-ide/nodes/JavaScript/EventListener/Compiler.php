<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class EventListenerCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $requestedEvent = trim((string) ($data['event_variable_name'] ?? 'e'));
        $event = JavaScriptNodeSupport::validIdentifier($requestedEvent)
            ? $requestedEvent
            : 'e';
        if ($event !== $requestedEvent && $requestedEvent !== '') {
            $context->addWarning('Event Listener used an invalid event variable name; e was used instead.');
        }
        $element = $context->javascriptVariable($node, 'element');
        if ($context->mode() === 'javascript_expression') {
            $outputs = [
                'event_value' => $event,
                'element' => $element,
                'value' => "({$element} && 'value' in {$element} ? {$element}.value : null)",
            ];
            $port = (string) ($context->outputPort() ?? 'event_value');
            return ['expression' => $outputs[$port] ?? 'null', 'outputs' => $outputs];
        }
        $preventDefault = !empty($data['prevent_default']);
        $passive = !empty($data['passive']);
        if ($preventDefault && $passive) {
            $context->addWarning('Event Listener cannot be passive while Prevent Default is enabled; passive mode was disabled.');
            $passive = false;
        }
        return [
            'control' => 'event_listener',
            'event_name' => (string) ($data['event_name'] ?? 'click'),
            'selector' => JavaScriptNodeSupport::targetSelector($node, $context),
            'event_variable' => $event,
            'element_variable' => $element,
            'event_port' => 'event',
            'prevent_default' => $preventDefault,
            'stop_propagation' => !empty($data['stop_propagation']),
            'capture' => !empty($data['capture']),
            'once' => !empty($data['once']),
            'passive' => $passive,
        ];
    }
}
