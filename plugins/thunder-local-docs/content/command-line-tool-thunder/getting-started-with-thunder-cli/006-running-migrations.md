---
title: "Running Migrations"
slug: "running-migrations"
description: "How to execute pending migrations for one plugin or all plugins, and what Thunder does behind the scenes."
published: true
order: 6
source_id: 154
keywords: ["running", "migrations", "execute", "pending", "one", "plugin", "all", "plugins", "thunder", "does", "behind", "scenes", "command", "line", "tool", "getting", "started", "cli", "step", "run", "migration", "internally", "single", "file"]
---

**Step 4: run the migration**

To run pending migrations for one plugin:

```php
php thunder migrate blog
```

To run pending migrations across every plugin:

```php
php thunder migrate all
```

**What Thunder does internally**

When you run `migrate`, Thunder:

1. ensures the migration tracking table exists
2. resolves the target plugin folder or all plugin folders
3. loads migration files from each plugin's `migrations/` folder
4. sorts them by filename
5. skips any migration already recorded as run
6. loads the class and executes `up()`
7. records the migration in the tracking table

**Running a single migration file**

You can also target a specific filename if needed:

```php
php thunder migrate blog 2026-04-21_123456_create-posts-table.php
```

That only runs the specified migration if it has not already been tracked as run.

**Practical beginner tip**

After every migration run, immediately check status:

```php
php thunder migrate:status blog
```

That confirms whether the migration was recorded successfully.
