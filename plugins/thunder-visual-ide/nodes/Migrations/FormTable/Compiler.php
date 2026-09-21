<?php

declare(strict_types=1);

namespace ThunderVisualIde\Nodes\Migrations;

use ThunderVisualIde\Compiler\CompileContext;
use ThunderVisualIde\Compiler\NodeCompilerInterface;

final class FormTableCompiler implements NodeCompilerInterface
{
    public function compile(array $node, CompileContext $context): array
    {
        if ($context->mode() !== 'migration')
        {
            return [];
        }
        $d = is_array($node['data'] ?? null) ? $node['data'] : [];
        $schema = $context->formSchema((string)($d['view_node_id'] ?? ''), (string)($d['form_name'] ?? ''));
        if ($schema === null)
        {
            $context->addError('Create Table From Form could not find the selected form schema.');
            return [];
        }
        $table = trim((string)($d['table'] ?? ''));
        if ($table === '')
        {
            $context->addError('Create Table From Form needs a table name.');
            return [];
        }
        $up = [];
        if (!empty($d['include_id']))
        {
            $up[] = '$this->addColumn("id INT UNSIGNED NOT NULL AUTO_INCREMENT");';
            $up[] = '$this->addPrimaryKey("id");';
        }
        foreach ((array)($schema['fields'] ?? []) as $field)
        {
            $name = preg_replace('/[^A-Za-z0-9_]/', '', (string)($field['name'] ?? ''));
            if ($name === '')
            {
                continue;
            }
            $type = (string)($field['node_type'] ?? '');
            $required = in_array('required', (array)($field['rules'] ?? []), true);
            $sql = match ($type)
            {
                'view.number_input' => 'INT',
                'view.date_input' => 'DATE',
                'view.textarea' => 'TEXT',
                'view.checkbox' => 'TINYINT(1)',
                'view.file_input' => 'VARCHAR(255)',
                default => 'VARCHAR(255)'
            };
            $null = $required ? ' NOT NULL' : ' NULL';
            $up[] = '$this->addColumn(' . $context->export($name . ' ' . $sql . $null) . ');';
        }
        if (!empty($d['include_timestamps']))
        {
            $up[] = '$this->addColumn("date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");';
            $up[] = '$this->addColumn("date_updated DATETIME NULL");';
        }
        foreach (array_filter(array_map('trim', explode(',', (string)($d['unique_fields'] ?? '')))) as $field)
        {
            $indexName = 'uniq_' . $table . '_' . $field;
            $up[] = sprintf(
                '$this->addUniqueKey(%s, %s);',
                $context->export($field),
                $context->export($indexName)
            );
        }
        $up[] = '$this->createTable(' . $context->export($table) . ');';
        return [
            'up' => implode("\n", $up),
            'down' => '$this->dropTable(' . $context->export($table) . ');',
        ];
    }
}
