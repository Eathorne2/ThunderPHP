<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Models;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ModelCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $className = $context->studly(
            (string) ($data['class_name'] ?? 'Model')
        );
        $table = (string) ($data['table'] ?? '');
        $fillableInsert = $this->csv(
            (string) ($data['fillable_insert'] ?? '')
        );
        $fillableUpdate = $this->csv(
            (string) ($data['fillable_update'] ?? '')
        );
        $methods = $this->decodeMethods($data, $className, $context);
        $methodCode = $this->compileMethods($methods, $context);

        $php = "<?php\n\ndeclare(strict_types=1);\n\n";
        $php .= 'namespace ' . $context->pluginNamespace() . ";\n\n";
        $php .= "class {$className} extends \\Model\\Model\n{\n";
        $php .= '    protected string $table = '
            . $context->export($table)
            . ";\n\n";
        $php .= '    protected array $fillableInsert = '
            . $context->export($fillableInsert)
            . ";\n\n";
        $php .= '    protected array $fillableUpdate = '
            . $context->export($fillableUpdate)
            . ';';

        if ($methodCode !== []) {
            $php .= "\n\n" . implode("\n\n", $methodCode);
        }

        $php .= "\n}\n";

        return [
            'files' => [
                "models/{$className}.php" => $php,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return list<array<string, mixed>>
     */
    private function decodeMethods(
        array $data,
        string $className,
        CompileContext $context
    ): array {
        $methods = json_decode(
            (string) ($data['methods_json'] ?? '[]'),
            true
        );

        if (!is_array($methods)) {
            $context->addError(
                "Model {$className} has invalid methods JSON."
            );
            return [];
        }

        return array_values(array_filter($methods, 'is_array'));
    }

    /**
     * @param list<array<string, mixed>> $methods
     * @return list<string>
     */
    private function compileMethods(
        array $methods,
        CompileContext $context
    ): array {
        $compiledMethods = [];

        foreach ($methods as $method) {
            $methodName = preg_replace(
                '/[^A-Za-z0-9_]/',
                '',
                (string) ($method['name'] ?? '')
            );
            if ($methodName === '') {
                continue;
            }

            $parameters = $this->parameters(
                (array) ($method['params'] ?? [])
            );
            $returnType = $methodName === '__construct'
                ? ''
                : $this->type((string) ($method['return_type'] ?? ''));
            $returnCode = $returnType !== '' ? ': ' . $returnType : '';
            $body = $this->methodBody($method, $context);

            $signature = '    public function '
                . $methodName
                . '('
                . implode(', ', $parameters)
                . ')'
                . $returnCode;

            $compiledMethods[] = $signature
                . "\n    {\n"
                . $context->indent($body, 2)
                . "\n    }";
        }

        return $compiledMethods;
    }

    /** @param array<string, mixed> $method */
    private function methodBody(
        array $method,
        CompileContext $context
    ): string {
        $implementation = (string) ($method['implementation'] ?? 'body');
        $graphId = (string) ($method['graph_id'] ?? '');

        if ($implementation === 'flow' && $graphId !== '') {
            $body = $context->compileFlowGraph($graphId);
        } else {
            $body = trim((string) ($method['body'] ?? ''));
        }

        if ($body !== '') {
            return $body;
        }

        return $implementation === 'flow'
            ? '// No connected statements.'
            : '// Add method implementation.';
    }

    /** @return list<string> */
    private function csv(string $value): array
    {
        $items = array_map('trim', explode(',', $value));

        return array_values(array_filter(
            $items,
            static fn (string $item): bool => $item !== ''
        ));
    }

    /**
     * @param array<int, mixed> $items
     * @return list<string>
     */
    private function parameters(array $items): array
    {
        $parameters = [];

        foreach ($items as $parameter) {
            $metadata = is_array($parameter)
                ? $parameter
                : ['name' => $parameter, 'type' => ''];
            $name = preg_replace(
                '/[^A-Za-z0-9_]/',
                '',
                (string) ($metadata['name'] ?? '')
            );
            if ($name === '') {
                continue;
            }

            $type = $this->type((string) ($metadata['type'] ?? ''));
            $default = $this->parameterDefault(
                (string) ($metadata['default'] ?? '')
            );
            $parameters[] = ($type !== '' ? $type . ' ' : '')
                . '$'
                . $name
                . ($default !== '' ? ' = ' . $default : '');
        }

        return $parameters;
    }

    private function parameterDefault(string $value): string
    {
        $value = trim(str_replace(["\r", "\n"], ' ', $value));

        if ($value === '') {
            return '';
        }

        return rtrim($value, ';');
    }

    private function type(string $type): string
    {
        return preg_replace(
            '/[^A-Za-z0-9_\\\\|&?]/',
            '',
            trim($type)
        ) ?: '';
    }
}
