---
title: "Migration Commands"
slug: "migration-commands"
description: "Reference for migrate, rollback, refresh, and status commands exposed by the Thunder CLI."
published: true
order: 6
source_id: 136
keywords: ["migration", "commands", "reference", "migrate", "rollback", "refresh", "status", "exposed", "thunder", "cli", "command", "line", "tool", "usage", "overview", "available", "labels", "work", "practical", "workflow", "important", "behavior"]
---

**Overview**

Thunder exposes migration commands through the `migrate()` command dispatcher. These commands operate on one plugin or all plugins.

**Available commands**

```php
php thunder migrate <plugin|all> [filename]
php thunder migrate:rollback <plugin|all>
php thunder migrate:refresh <plugin> [filename]
php thunder migrate:status [plugin|all]
```

**migrate**

Runs pending migrations.

```php
php thunder migrate blog
```

Run all plugins:

```php
php thunder migrate all
```

Run one specific migration file if it is still pending:

```php
php thunder migrate blog 2026-04-21_153000_create-posts-table.php
```

**migrate:rollback**

Rolls back the latest migration batch.

```php
php thunder migrate:rollback blog
```

Or for all plugins:

```php
php thunder migrate:rollback all
```

**migrate:refresh**

Performs a rollback followed by a migrate.

```php
php thunder migrate:refresh blog
```

This is useful during development when rebuilding a plugin schema repeatedly.

**migrate:status**

Shows which migration files have already run and which are still pending.

```php
php thunder migrate:status blog
php thunder migrate:status all
```

**How status labels work**

The status output marks migrations as:

- `[RAN]`
- `[PENDING]`

For ran migrations, it also shows:

- batch number
- ran timestamp

**Practical workflow**

```php
// Create the migration file
php thunder make:migration blog create-posts-table

// Run it
php thunder migrate blog

// Confirm it ran
php thunder migrate:status blog

// Roll it back if needed
php thunder migrate:rollback blog
```

**Important behavior**

A migration file is skipped during `migrate` if Thunder already logged it as having run. That protects you from re-running the same migration accidentally unless you remove its record through rollback.
