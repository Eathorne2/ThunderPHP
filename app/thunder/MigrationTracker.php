<?php

namespace Thunder;

defined('ROOTPATH') or die("Direct script access denied");

use Thunder\Database;

class MigrationTracker extends Database
{
    protected string $table = 'thunder_migrations';

    public function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            plugin_id VARCHAR(150) NOT NULL,
            migration_name VARCHAR(255) NOT NULL,
            batch INT NOT NULL DEFAULT 1,
            checksum VARCHAR(64) DEFAULT NULL,
            ran_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_plugin_migration (plugin_id, migration_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

        $this->query($sql);
    }

    public function hasRun(string $pluginId, string $migrationName): bool
    {
        $row = $this->get_row(
            "SELECT id FROM {$this->table} WHERE plugin_id = :plugin_id AND migration_name = :migration_name LIMIT 1",
            [
                'plugin_id' => $pluginId,
                'migration_name' => $migrationName,
            ]
        );

        return !empty($row->id);
    }

    public function logRun(string $pluginId, string $migrationName, int $batch, string $checksum = ''): bool
    {
        $this->query(
            "INSERT INTO {$this->table} (plugin_id, migration_name, batch, checksum)
             VALUES (:plugin_id, :migration_name, :batch, :checksum)",
            [
                'plugin_id' => $pluginId,
                'migration_name' => $migrationName,
                'batch' => $batch,
                'checksum' => $checksum,
            ]
        );

        return !$this->has_error;
    }

    public function removeRun(string $pluginId, string $migrationName): bool
    {
        $this->query(
            "DELETE FROM {$this->table} WHERE plugin_id = :plugin_id AND migration_name = :migration_name",
            [
                'plugin_id' => $pluginId,
                'migration_name' => $migrationName,
            ]
        );

        return !$this->has_error;
    }

    public function getNextBatch(): int
    {
        $row = $this->get_row("SELECT MAX(batch) AS batch FROM {$this->table}");
        return !empty($row->batch) ? ((int)$row->batch + 1) : 1;
    }

    public function getLastBatchMigrations(?string $pluginId = null): array
    {
        if ($pluginId) {
            $row = $this->get_row(
                "SELECT MAX(batch) AS batch FROM {$this->table} WHERE plugin_id = :plugin_id",
                ['plugin_id' => $pluginId]
            );

            $batch = (int)($row->batch ?? 0);
            if (!$batch) return [];

            return $this->query(
                "SELECT * FROM {$this->table}
                 WHERE plugin_id = :plugin_id AND batch = :batch
                 ORDER BY id DESC",
                [
                    'plugin_id' => $pluginId,
                    'batch' => $batch,
                ]
            ) ?: [];
        }

        $row = $this->get_row("SELECT MAX(batch) AS batch FROM {$this->table}");
        $batch = (int)($row->batch ?? 0);
        if (!$batch) return [];

        return $this->query(
            "SELECT * FROM {$this->table} WHERE batch = :batch ORDER BY id DESC",
            ['batch' => $batch]
        ) ?: [];
    }

    public function allRuns(?string $pluginId = null): array
    {
        if ($pluginId) {
            return $this->query(
                "SELECT * FROM {$this->table} WHERE plugin_id = :plugin_id ORDER BY id ASC",
                ['plugin_id' => $pluginId]
            ) ?: [];
        }

        return $this->query("SELECT * FROM {$this->table} ORDER BY id ASC") ?: [];
    }
}
