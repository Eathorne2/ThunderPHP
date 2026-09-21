<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Charts;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class DatasetCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $nodeId = (string) ($node['id'] ?? '');
        $items = [
            "'label' => " . $context->export((string) ($data['label'] ?? 'Dataset')),
            "'data' => " . $context->inputExpression($nodeId, 'data', '[]'),
            "'backgroundColor' => " . $context->inputExpression(
                $nodeId,
                'background_colors',
                $context->export((string) ($data['background_color'] ?? 'rgba(37, 99, 235, 0.35)'))
            ),
            "'borderColor' => " . $context->inputExpression(
                $nodeId,
                'border_colors',
                $context->export((string) ($data['border_color'] ?? '#2563eb'))
            ),
            "'borderWidth' => " . max(0, (float) ($data['border_width'] ?? 2)),
            "'fill' => " . (!empty($data['fill']) ? 'true' : 'false'),
            "'tension' => " . max(0, min(1, (float) ($data['tension'] ?? 0.25))),
            "'hidden' => " . (!empty($data['hidden']) ? 'true' : 'false'),
            "'pointRadius' => " . max(0, (float) ($data['point_radius'] ?? 3)),
        ];

        $type = trim((string) ($data['chart_type'] ?? 'inherit'));
        if ($type !== '' && $type !== 'inherit') {
            $items[] = "'type' => " . $context->export($type);
        }
        $axis = trim((string) ($data['y_axis_id'] ?? ''));
        if ($axis !== '') {
            $items[] = "'yAxisID' => " . $context->export($axis);
        }
        $stack = trim((string) ($data['stack'] ?? ''));
        if ($stack !== '') {
            $items[] = "'stack' => " . $context->export($stack);
        }

        $base = '[' . implode(', ', $items) . ']';
        $advanced = $context->inputExpression($nodeId, 'advanced_options', '[]');
        $expression = '(static function (array $dataset, $advanced): array {'
            . ' return is_array($advanced) ? array_replace_recursive($dataset, $advanced) : $dataset;'
            . '})(' . $base . ', ' . $advanced . ')';

        return [
            'expression' => $expression,
            'outputs' => ['dataset' => $expression],
        ];
    }
}
