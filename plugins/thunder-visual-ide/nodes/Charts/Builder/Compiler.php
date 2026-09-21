<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Charts;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class BuilderCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $variable = $context->variableForNode($node, 'chart_config');
        if ($context->mode() === 'expression') {
            return ['expression' => $variable, 'outputs' => ['config' => $variable]];
        }

        $nodeId = (string) ($node['id'] ?? '');
        $count = max(1, min(20, (int) ($data['dataset_count'] ?? 1)));
        $datasetExpressions = [];
        $connected = 0;
        for ($i = 1; $i <= $count; $i++) {
            if ($context->incomingEdges($nodeId, 'dataset_' . $i) !== []) {
                $connected++;
            }
            $datasetExpressions[] = $context->inputExpression($nodeId, 'dataset_' . $i, '[]');
        }
        if ($connected === 0) {
            $context->addWarning('Chart Builder has no connected datasets.');
        }

        $type = trim((string) ($data['chart_type'] ?? 'bar')) ?: 'bar';
        $labels = $context->inputExpression($nodeId, 'labels', '[]');
        $advanced = $context->inputExpression($nodeId, 'advanced_options', '[]');
        $options = [
            'responsive' => !empty($data['responsive']),
            'maintainAspectRatio' => !empty($data['maintain_aspect_ratio']),
            'animation' => !empty($data['animation']),
            'indexAxis' => (string) ($data['index_axis'] ?? 'x'),
            'interaction' => [
                'mode' => (string) ($data['interaction_mode'] ?? 'nearest'),
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'display' => !empty($data['show_legend']),
                    'position' => (string) ($data['legend_position'] ?? 'top'),
                ],
                'title' => [
                    'display' => !empty($data['show_title']),
                    'text' => (string) ($data['chart_title'] ?? 'Chart'),
                ],
                'tooltip' => ['enabled' => !empty($data['tooltip_enabled'])],
            ],
        ];
        if (in_array($type, ['bar', 'line', 'scatter', 'bubble'], true)) {
            $options['scales'] = [
                'x' => ['stacked' => !empty($data['stacked'])],
                'y' => [
                    'stacked' => !empty($data['stacked']),
                    'beginAtZero' => !empty($data['begin_at_zero']),
                ],
            ];
        }

        $baseOptions = $context->export($options);
        $datasets = '[' . implode(', ', $datasetExpressions) . ']';
        $labelsVariable = $variable . '_labels';
        $advancedVariable = $variable . '_advanced_options';
        $baseOptionsVariable = $variable . '_base_options';
        $key = trim((string) ($data['data_key'] ?? 'chart')) ?: 'chart';
        $code = [];
        $code[] = $labelsVariable . ' = ' . $labels . ';';
        $code[] = $advancedVariable . ' = ' . $advanced . ';';
        $code[] = $baseOptionsVariable . ' = ' . $baseOptions . ';';
        $code[] = $variable . ' = [';
        $code[] = "    'type' => " . $context->export($type) . ',';
        $code[] = "    'data' => [";
        $code[] = "        'labels' => is_array({$labelsVariable}) ? array_values({$labelsVariable}) : [],";
        $code[] = "        'datasets' => array_values(array_filter({$datasets}, static fn (mixed \$dataset): bool => is_array(\$dataset) && \$dataset !== [])),";
        $code[] = "    ],";
        $code[] = "    'options' => is_array({$advancedVariable}) ? array_replace_recursive({$baseOptionsVariable}, {$advancedVariable}) : {$baseOptionsVariable},";
        $code[] = '];';
        $code[] = 'set_value(' . $context->export($key) . ', ' . $variable . ');';

        return [
            'code' => implode("\n", $code),
            'next_port' => 'exec',
            'outputs' => ['config' => $variable],
        ];
    }
}
