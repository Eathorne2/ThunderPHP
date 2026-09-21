<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class MigrationCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'architecture') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $timestamp = preg_replace(
            '/[^0-9_-]/',
            '',
            (string) ($data['timestamp'] ?? '')
        ) ?: date('Y-m-d_His');

        $slug = strtolower(trim(
            preg_replace(
                '/[^A-Za-z0-9_]+/',
                '_',
                (string) ($data['filename_slug'] ?? 'migration')
            ),
            '_'
        )) ?: 'migration';

        $graphId = (string) ($data['graph_id'] ?? '');
        $compiledGraph = $graphId !== ''
            ? $context->compileMigrationGraph($graphId)
            : ['up' => '', 'down' => ''];

        $className = ucfirst($slug);
        $upCode = trim((string) $compiledGraph['up']);
        $downCode = trim((string) $compiledGraph['down']);

        $template = <<<'PHP'
<?php

declare(strict_types=1);

namespace Migration;

class %s extends Migration
{
    public function up()
    {
%s
    }

    public function down()
    {
%s
    }
}
PHP;

        $content = sprintf(
            $template,
            $className,
            $context->indent($upCode, 2),
            $context->indent($downCode, 2)
        ) . "\n";

        $filename = "migrations/{$timestamp}_{$slug}.php";

        return [
            'files' => [
                $filename => $content,
            ],
        ];
    }
}
