<?php

declare(strict_types=1);

namespace ThunderVisualIde\Node;

use RuntimeException;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class NodeRegistry
{
    /** @var array<string,array<string,mixed>> */
    private array $definitions = [];

    /** @var array<string,NodeCompilerInterface> */
    private array $compilers = [];

    public function __construct(private readonly string $nodesPath)
    {
        $this->load();
    }

    /** @return array<string,array<string,mixed>> */
    public function all(): array
    {
        return $this->definitions;
    }

    /** @return array<string,array<string,mixed>> */
    public function publicDefinitions(): array
    {
        $definitions = [];
        foreach ($this->definitions as $type => $definition) {
            unset($definition['_directory'], $definition['_compiler_file']);
            $definitions[$type] = $definition;
        }
        return $definitions;
    }

    /** @return array<string,mixed> */
    public function definition(string $type): array
    {
        if (!isset($this->definitions[$type])) {
            throw new RuntimeException("Unknown node type: {$type}");
        }
        return $this->definitions[$type];
    }

    public function compiler(string $type): NodeCompilerInterface
    {
        if (isset($this->compilers[$type])) {
            return $this->compilers[$type];
        }

        $definition = $this->definition($type);
        $class = (string)($definition['compiler']['class'] ?? '');
        $file = (string)($definition['_compiler_file'] ?? '');

        if ($class === '' || $file === '' || !is_file($file)) {
            throw new RuntimeException("Node {$type} does not have a usable compiler.");
        }

        require_once $file;
        if (!class_exists($class)) {
            throw new RuntimeException("Compiler class {$class} was not found for node {$type}.");
        }

        $compiler = new $class();
        if (!$compiler instanceof NodeCompilerInterface) {
            throw new RuntimeException("Compiler {$class} must implement NodeCompilerInterface.");
        }

        return $this->compilers[$type] = $compiler;
    }

    private function load(): void
    {
        if (!is_dir($this->nodesPath)) {
            throw new RuntimeException('The node definitions directory does not exist.');
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->nodesPath, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getFilename() !== 'node.json') {
                continue;
            }

            $raw = file_get_contents($file->getPathname());
            $definition = json_decode((string)$raw, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($definition)) {
                continue;
            }

            $type = trim((string)($definition['type'] ?? ''));
            if ($type === '') {
                throw new RuntimeException('A node definition is missing its type: ' . $file->getPathname());
            }
            if (isset($this->definitions[$type])) {
                throw new RuntimeException("Duplicate node type {$type}.");
            }

            $directory = $file->getPath();
            $definition['_directory'] = $directory;
            $definition['package_path'] = str_replace(DIRECTORY_SEPARATOR, '/', ltrim(substr($directory, strlen($this->nodesPath)), DIRECTORY_SEPARATOR));
            $definition['_compiler_file'] = $directory . DIRECTORY_SEPARATOR . (string)($definition['compiler']['file'] ?? 'Compiler.php');
            $definition['properties'] = is_array($definition['properties'] ?? null) ? $definition['properties'] : [];
            $definition['ports'] = is_array($definition['ports'] ?? null) ? $definition['ports'] : ['inputs' => [], 'outputs' => []];
            $definition['defaults'] = is_array($definition['defaults'] ?? null) ? $definition['defaults'] : [];
            $definition['allowed_graphs'] = is_array($definition['allowed_graphs'] ?? null) ? $definition['allowed_graphs'] : ['architecture', 'flow'];

            $this->definitions[$type] = $definition;
        }

        uasort($this->definitions, static function (array $a, array $b): int {
            $group = strcmp((string)($a['category'] ?? ''), (string)($b['category'] ?? ''));
            return $group !== 0 ? $group : strcmp((string)($a['label'] ?? ''), (string)($b['label'] ?? ''));
        });
    }
}
