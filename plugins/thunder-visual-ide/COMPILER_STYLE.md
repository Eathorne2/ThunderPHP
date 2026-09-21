# Compiler Source Style

Node compilers are normal PHP source files intended to be read, copied, and modified. Keep them clear even when a shorter expression would work.

## Recommended structure

```php
<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Example;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ExampleCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null)
            ? $node['data']
            : [];

        $value = (string) ($data['value'] ?? '');
        $expression = $context->export($value);

        return [
            'expression' => $expression,
            'outputs' => [
                'value' => $expression,
            ],
        ];
    }
}
```

## Conventions

- Use `declare(strict_types=1);`.
- Use descriptive names such as `$data`, `$expression`, `$routeName`, and `$methodCode`.
- Keep source lines at 120 characters or fewer.
- Put returned contribution arrays on multiple lines.
- Store repeated or complex expressions in named variables before returning them.
- Use small private helper methods when one `compile()` method starts doing several jobs.
- Use `sprintf()`, `implode()`, heredoc, or nowdoc for multi-line generated PHP instead of one long concatenated line.
- Check `$context->mode()` early when a compiler only supports one mode.
- Add compiler errors with `$context->addError()` rather than throwing for normal user mistakes.
- Keep framework-specific behavior visible. For example, use `Core\Request`, `Core\Validate`, ThunderPHP helpers, hooks, and migration methods instead of hiding them behind generic replacements.

## Expressions

Expression nodes normally return both `expression` and their named output:

```php
return [
    'expression' => $expression,
    'outputs' => [
        'value' => $expression,
    ],
];
```

## Flow statements

```php
return [
    'code' => $code,
    'next_port' => 'exec',
];
```

Terminal statements use:

```php
return [
    'code' => $code,
    'terminal' => true,
];
```

## Branches

```php
return [
    'control' => 'if',
    'before' => $setupCode,
    'condition' => $condition,
    'true_port' => 'true',
    'false_port' => 'false',
];
```

## Architecture contributions

```php
return [
    'routes' => [$route],
    'hooks' => [$hookCode],
    'files' => [
        $path => $contents,
    ],
];
```

## Building readable generated blocks

The compiler source should remain readable even when it creates a larger PHP block:

```php
$template = <<<'PHP'
add_action(%s, function () {
%s
}, %d, %s);
PHP;

$hookCode = sprintf(
    $template,
    $context->export($hookName),
    $context->indent($controllerCode),
    $priority,
    $context->export($routeName)
);
```

This is preferable to placing the complete hook in one concatenated source line.

## Result variable naming

Use `CompileContext::variableForNode()` for generated temporary/result variables. This permits users to provide an optional readable variable name while preserving deterministic automatic names for older projects.

```php
$result = $context->variableForNode($node, 'result');
```

Do not concatenate node IDs manually when the value is user-visible or exposed through an output port.
