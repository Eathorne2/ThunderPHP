<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Images;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class ImageCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        $variable = $context->variableForNode($node, 'image');

        if ($context->mode() === 'expression') {
            return [
                'expression' => $variable,
                'outputs' => [
                    'image' => $variable,
                ],
            ];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $jpegQuality = $this->boundedInteger($data['jpeg_quality'] ?? 85, 0, 100);
        $pngCompression = $this->boundedInteger($data['png_compression'] ?? 7, 0, 9);
        $webpQuality = $this->boundedInteger($data['webp_quality'] ?? 80, 0, 100);
        $allowUpscale = !empty($data['allow_upscale']) ? 'true' : 'false';
        $hashedNames = !empty($data['use_hashed_names']) ? 'true' : 'false';

        $literalThumbnailDir = trim((string) ($data['thumbnail_dir'] ?? ''));
        $thumbnailDir = $context->inputExpression(
            (string) $node['id'],
            'thumbnail_dir',
            $literalThumbnailDir === '' ? 'null' : $context->export($literalThumbnailDir)
        );

        $lines = [
            "{$variable} = new \\Core\\Image();",
            "{$variable}->set_jpeg_quality({$jpegQuality});",
            "{$variable}->set_png_compression({$pngCompression});",
            "{$variable}->set_webp_quality({$webpQuality});",
            "{$variable}->allow_upscale({$allowUpscale});",
            "{$variable}->use_hashed_names({$hashedNames});",
        ];

        if ($thumbnailDir !== 'null') {
            $lines[] = "{$variable}->set_thumbnail_dir({$thumbnailDir});";
        }

        return [
            'code' => implode("\n", $lines),
            'next_port' => 'exec',
            'outputs' => [
                'image' => $variable,
            ],
        ];
    }

    private function boundedInteger(mixed $value, int $minimum, int $maximum): int
    {
        return max($minimum, min($maximum, (int) $value));
    }
}
