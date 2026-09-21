<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Custom;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ClassDefinitionCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $className = $context->studly(
            (string) ($data['class_name'] ?? 'CustomClass')
        );
        $parentClass = $this->className((string) ($data['extends'] ?? ''));
        $interfaces = $this->interfaces(
            (string) ($data['implements'] ?? '')
        );
        $methods = $this->decodeMethods($data, $className, $context);
        $methodCode = $this->compileMethods($methods, $context);
        $declaration = $this->classDeclaration(
            $className,
            $parentClass,
            $interfaces
        );

        $php = "<?php\n\ndeclare(strict_types=1);\n\n";
        $php .= 'namespace ' . $context->pluginNamespace() . ";\n\n";
        $php .= $declaration . "\n{\n";

        if ($methodCode !== []) {
            $php .= implode("\n\n", $methodCode) . "\n";
        }

        $php .= "}\n";

        return [
            'bootstrap' => [
                "require_once plugin_path('classes/{$className}.php');",
            ],
            'files' => [
                "classes/{$className}.php" => $php,
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
                "Class {$className} has invalid methods JSON."
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

            $visibility = in_array(
                $method['visibility'] ?? '',
                ['public', 'protected', 'private'],
                true
            ) ? (string) $method['visibility'] : 'public';
            $staticKeyword = $methodName !== '__construct'
                && !empty($method['static'])
                    ? ' static'
                    : '';
            $parameters = $this->parameters(
                (array) ($method['params'] ?? [])
            );
            $returnType = $methodName === '__construct'
                ? ''
                : $this->type((string) ($method['return_type'] ?? ''));
            $returnCode = $returnType !== '' ? ': ' . $returnType : '';
            $body = $this->methodBody($method, $context);

            $signature = "    {$visibility}{$staticKeyword} function "
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
        $implementation = (string) ($method['implementation'] ?? 'flow');
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

    /**
     * @param list<string> $interfaces
     */
    private function classDeclaration(
        string $className,
        string $parentClass,
        array $interfaces
    ): string {
        $declaration = "class {$className}";

        if ($parentClass !== '') {
            $declaration .= ' extends ' . $parentClass;
        }
        if ($interfaces !== []) {
            $declaration .= ' implements ' . implode(', ', $interfaces);
        }

        return $declaration;
    }

    /** @return list<string> */
    private function interfaces(string $value): array
    {
        $interfaces = array_map(
            fn (string $name): string => $this->className($name),
            explode(',', $value)
        );

        return array_values(array_filter($interfaces));
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

    private function className(string $name): string
    {
        return preg_replace(
            '/[^A-Za-z0-9_\\\\]/',
            '',
            trim($name)
        ) ?: '';
    }
}
