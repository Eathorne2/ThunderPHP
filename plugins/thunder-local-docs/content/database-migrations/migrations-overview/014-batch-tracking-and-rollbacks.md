---
title: "Batch Tracking and Rollbacks"
slug: "batch-tracking-and-rollbacks"
description: "Detailed explanation of migration batches, rollback behavior, refresh behavior, and status reporting."
published: true
order: 14
source_id: 144
keywords: ["batch", "tracking", "rollbacks", "detailed", "explanation", "migration", "batches", "rollback", "behavior", "refresh", "status", "reporting", "database", "migrations", "overview", "why", "matter", "order", "practical", "example", "best", "practice"]
---

**Overview**

Thunder tracks migrations in batches. A batch represents one migration run operation. This makes rollback behavior predictable.

**What is a batch**

Whenever Thunder runs pending migrations in `up` mode, it asks the migration tracker for the next batch number. Every migration that runs during that command is recorded under that same batch.

Example:

```php
php thunder migrate blog
```

If the next batch is `3`, all migrations executed in that command are logged with batch `3`.

**Why batches matter**

Rollback is batch-based. When you run:

```php
php thunder migrate:rollback blog
```

Thunder does not roll back every migration ever run. It rolls back only the **latest batch** for that plugin.

For `all`, it rolls back the latest global batch returned by the tracker.

**Rollback order**

During rollback, Thunder reverses file order using descending sorting. This is important because schema teardown usually needs the most recent changes undone first.

**Refresh behavior**

`migrate:refresh` is implemented as:

1. rollback
2. migrate

So it destroys the latest batch and immediately rebuilds it.

```php
php thunder migrate:refresh blog
```

This is especially useful during plugin development.

**Status reporting**

`migrate:status` compares migration files on disk with records in the migration tracker table. It then prints either:

- `[RAN]`
- `[PENDING]`

For ran migrations, Thunder shows metadata such as:

- batch number
- run timestamp

**Practical example**

Suppose the blog plugin has these migration files:

```php
2026-04-21_100000_create-posts-table.php
2026-04-21_101000_add-status-to-posts.php
2026-04-21_102000_create-comments-table.php
```

If you run:

```php
php thunder migrate blog
```

all three may be logged in the same batch, for example batch `5`.

Running:

```php
php thunder migrate:rollback blog
```

will attempt to roll back those migrations in reverse order.

**Best practice**

Treat each migration run as a logical unit. If you add several related migration files and run them together, they will typically belong to one rollback batch.
