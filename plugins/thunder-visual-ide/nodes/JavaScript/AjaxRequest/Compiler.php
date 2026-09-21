<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\JavaScript;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\JavaScriptNodeSupport;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class AjaxRequestCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $requestedXhr = trim((string) ($data['xhr_variable_name'] ?? 'xhr')) ?: 'xhr';
        $xhr = JavaScriptNodeSupport::validIdentifier($requestedXhr)
            ? $requestedXhr
            : $context->javascriptVariable($node, 'xhr');
        if ($xhr !== $requestedXhr && $requestedXhr !== '') {
            $context->addWarning('AJAX Request used an invalid XHR variable name; an internal safe name was used instead.');
        }

        $response = $context->javascriptVariable($node, 'response');
        $error = $context->javascriptVariable($node, 'error');
        $progress = $context->javascriptVariable($node, 'progress');
        $status = $context->javascriptVariable($node, 'status');

        if ($context->mode() === 'javascript_expression') {
            $outputs = ['xhr'=>$xhr,'response'=>$response,'error'=>$error,'progress'=>$progress,'status'=>$status];
            $port = (string) ($context->outputPort() ?? 'response');
            return ['expression' => $outputs[$port] ?? 'null', 'outputs' => $outputs];
        }

        $url = $context->inputJavaScriptExpression((string) $node['id'], 'url', $context->javascriptExport((string) ($data['url'] ?? '')));
        $connectedBody = $context->inputJavaScriptExpression((string) $node['id'], 'body', 'null');
        $mode = (string) ($data['body_mode'] ?? 'Connected value');
        $formSelector = trim((string) ($data['form_selector'] ?? 'form')) ?: 'form';
        $formVar = $context->javascriptVariable($node, 'form');
        $formExpression = "(typeof {$formVar} !== 'undefined' && {$formVar} ? {$formVar} : document.querySelector(" . $context->javascriptExport($formSelector) . '))';
        $body = match ($mode) {
            'None' => 'null',
            'JSON' => 'JSON.stringify(' . $connectedBody . ')',
            'FormData' => '(function () { const value = ' . $connectedBody . '; if (value instanceof FormData) return value; const form = value && value.tagName === \'FORM\' ? value : ' . $formExpression . '; return form ? new FormData(form) : new FormData(); })()',
            default => $connectedBody,
        };
        $headersJson = trim((string) ($data['headers_json'] ?? '{}'));
        $headersFallback = JavaScriptNodeSupport::decodeObject($headersJson);
        $headersFallbackExpression = $headersFallback === [] && str_starts_with($headersJson, '{')
            ? '{}'
            : $context->javascriptExport($headersFallback);
        $headers = $context->inputJavaScriptExpression((string) $node['id'], 'headers', $headersFallbackExpression);
        $method = $context->javascriptExport(strtoupper((string) ($data['method'] ?? 'POST')));

        return [
            'control' => 'ajax',
            'xhr_variable' => $xhr,
            'response_variable' => $response,
            'error_variable' => $error,
            'progress_variable' => $progress,
            'status_variable' => $status,
            'method' => $method,
            'url' => $url,
            'body' => $body,
            'headers' => $headers,
            'timeout' => (string) max(0, (int) ($data['timeout_ms'] ?? 30000)),
            'response_type' => (string) ($data['response_type'] ?? 'text'),
            'parse_json' => !empty($data['parse_json']),
            'with_credentials' => !empty($data['with_credentials']),
        ];
    }
}
