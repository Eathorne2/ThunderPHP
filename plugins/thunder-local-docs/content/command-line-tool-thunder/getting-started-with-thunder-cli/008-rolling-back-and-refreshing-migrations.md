---
title: "Rolling Back and Refreshing Migrations"
slug: "rolling-back-and-refreshing-migrations"
description: "A beginner explanation of rollback batches, refresh behavior, and how to test schema changes safely."
published: true
order: 8
source_id: 156
keywords: ["rolling", "back", "refreshing", "migrations", "beginner", "explanation", "rollback", "batches", "refresh", "behavior", "test", "schema", "changes", "safely", "command", "line", "tool", "thunder", "getting", "started", "cli", "step", "roll", "needed"]
---

**Step 6: roll back changes when needed**

If you need to undo the latest migration batch, use:

```php
php thunder migrate:rollback blog
```

Or for all plugins:

```php
php thunder migrate:rollback all
```

**Important concept: rollback uses batches**

Thunder records each migration run in a batch. A rollback does not usually undo every migration ever made. It undoes the latest batch.

That means if you run three migrations together in one migrate command, they usually belong to one batch and may be rolled back together.

**Refresh command**

Thunder also supports:

```php
php thunder migrate:refresh blog
```

This performs:

1. rollback
2. migrate again

This is useful while developing and testing a migration repeatedly.

**When refresh is helpful**

Use `migrate:refresh` when:

- you just edited the migration logic
- you want to rebuild the same table from scratch
- you are still in development and not protecting production data yet

**Beginner warning**

Be careful with rollback and refresh on real systems. If a migration drops a table in `down()`, your data may be lost.
