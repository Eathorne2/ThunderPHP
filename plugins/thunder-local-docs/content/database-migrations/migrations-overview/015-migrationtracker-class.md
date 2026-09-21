---
title: "MigrationTracker Class"
slug: "migrationtracker-class"
description: "Reference for the migration tracker table and the methods that record, query, and remove migration runs."
published: true
order: 15
source_id: 145
keywords: ["migrationtracker", "class", "reference", "migration", "tracker", "table", "methods", "record", "query", "remove", "runs", "database", "migrations", "overview", "tracked", "fields", "ensuretable", "void", "hasrun", "string", "pluginid", "migrationname", "bool", "logrun"]
---

**Overview**

Thunder uses a dedicated `MigrationTracker` class to record which migrations have already run. This class extends `Thunder\Database` and stores migration history in the `thunder_migrations` table.

**Tracked fields**

The tracker table includes:

- `id`
- `plugin_id`
- `migration_name`
- `batch`
- `checksum`
- `ran_at`

It also defines a unique key across:

```php
(plugin_id, migration_name)
```

This means the same migration file name cannot be recorded twice for the same plugin.

**ensureTable(): void**

Creates the tracker table if it does not already exist.

Thunder calls this before migration operations so the tracker is always available.

**hasRun(string $pluginId, string $migrationName): bool**

Checks whether a migration has already been executed for a given plugin.

Thunder uses this to skip already-ran migrations during `migrate`.

**logRun(string $pluginId, string $migrationName, int $batch, string $checksum = ''): bool**

Stores a completed migration run in the tracker.

This happens after a successful `up()` call.

**removeRun(string $pluginId, string $migrationName): bool**

Deletes the migration record after a successful rollback.

This happens after a successful `down()` call.

**getNextBatch(): int**

Returns the next available batch number by reading the current maximum batch and adding `1`.

**getLastBatchMigrations(?string $pluginId = null): array**

Returns migrations from the latest batch, either:

- for one plugin, or
- globally when no plugin is provided

This is the core method used to decide what gets rolled back.

**allRuns(?string $pluginId = null): array**

Returns all tracked migration runs, optionally filtered by plugin.

This powers `migrate:status`.

**Why the tracker matters**

Without the tracker, Thunder would have no reliable way to know:

- which migrations are pending
- which migrations are already applied
- which migrations belong to the latest rollback batch
