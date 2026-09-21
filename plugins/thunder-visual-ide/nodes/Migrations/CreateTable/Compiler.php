<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class CreateTableCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration') {
            return [];
        }

        $data = is_array($node['data'] ?? null) ? $node['data'] : [];
        $table = trim((string) ($data['table'] ?? ''));
        if ($table === '') {
            $context->addError('Create Table needs a table name.');
            return [];
        }

        $columns = json_decode((string) ($data['columns_json'] ?? '[]'), true);
        if (!is_array($columns)) {
            $context->addError("Create Table {$table} contains invalid column data.");
            return [];
        }

        $up = [];
        foreach ($columns as $column) {
            if (!is_array($column)) {
                continue;
            }
            $definition = $this->columnDefinition($column);
            if ($definition !== '') {
                $up[] = '$this->addColumn(' . $context->export($definition) . ');';
            }
        }

        $primaryKeys = array_values(array_filter(array_map(
            'trim',
            explode(',', (string) ($data['primary_key'] ?? ''))
        )));
        if ($primaryKeys !== []) {
            $up[] = '$this->addPrimaryKey(' . $context->export(
                count($primaryKeys) === 1 ? $primaryKeys[0] : $primaryKeys
            ) . ');';
        }

        foreach (['engine' => 'addEngine', 'charset' => 'addCharset', 'collate' => 'addCollate'] as $key => $method) {
            $value = trim((string) ($data[$key] ?? ''));
            if ($value !== '') {
                $up[] = '$this->' . $method . '(' . $context->export($value) . ');';
            }
        }

        $up[] = '$this->createTable(' . $context->export($table) . ');';

        return [
            'up' => implode("\n", $up),
            'down' => '$this->dropTable(' . $context->export($table) . ');',
        ];
    }

    /** @param array<string,mixed> $column */
    private function columnDefinition(array $column): string
    {
        $name = preg_replace('/[^A-Za-z0-9_]/', '', (string) ($column['name'] ?? '')) ?: '';
        $type = strtoupper(preg_replace('/[^A-Za-z0-9_]/', '', (string) ($column['type'] ?? 'VARCHAR')) ?: 'VARCHAR');
        if ($name === '') {
            return '';
        }

        if ($type === 'ENUM') {
            $rawValues = $column['enum_values'] ?? $column['length'] ?? '';
            $values = is_array($rawValues) ? $rawValues : explode(',', (string) $rawValues);
            $values = array_values(array_filter(array_map(static function ($value): string {
                return trim((string) $value, " \t\n\r\0\x0B'\"");
            }, $values), static fn (string $value): bool => $value !== ''));
            if ($values === []) {
                $values = [''];
            }
            $quoted = array_map(static fn (string $value): string => "'" . str_replace("'", "''", $value) . "'", $values);
            $sql = $name . ' ENUM(' . implode(', ', $quoted) . ')';
        } else {
            $length = preg_replace('/[^0-9,]/', '', (string) ($column['length'] ?? '')) ?: '';
            $sql = $name . ' ' . $type . ($length !== '' ? '(' . $length . ')' : '');
            if (!empty($column['unsigned'])) {
                $sql .= ' UNSIGNED';
            }
        }
        $sql .= !empty($column['nullable']) ? ' NULL' : ' NOT NULL';

        $default = trim((string) ($column['default'] ?? ''));
        if ($default !== '') {
            $upper = strtoupper($default);
            if ($upper === 'NULL') {
                $sql .= ' DEFAULT NULL';
            } elseif ($type !== 'ENUM' && in_array($upper, ['CURRENT_TIMESTAMP', 'CURRENT_DATE', 'CURRENT_TIME'], true)) {
                $sql .= ' DEFAULT ' . $upper;
            } elseif ($type !== 'ENUM' && is_numeric($default)) {
                $sql .= ' DEFAULT ' . $default;
            } else {
                $sql .= " DEFAULT '" . str_replace("'", "''", $default) . "'";
            }
        }
        if (!empty($column['auto_increment'])) {
            $sql .= ' AUTO_INCREMENT';
        }
        $extra = trim((string) ($column['extra'] ?? ''));
        if ($extra !== '') {
            $sql .= ' ' . $extra;
        }
        return $sql;
    }
}
