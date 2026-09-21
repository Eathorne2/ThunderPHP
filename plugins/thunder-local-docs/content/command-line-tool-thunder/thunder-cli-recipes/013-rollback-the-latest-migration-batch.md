---
title: "Rollback the Latest Migration Batch"
slug: "rollback-the-latest-migration-batch"
description: "Undo the most recent migration batch by running down() on the latest logged files."
published: true
order: 13
source_id: 175
keywords: ["rollback", "latest", "migration", "batch", "undo", "most", "recent", "running", "down", "logged", "files", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to undo the most recent migration batch.

For one plugin:

```php
php thunder migrate:rollback blog
```

For all plugins:

```php
php thunder migrate:rollback all
```

Important behavior:

- rollback does not remove every migration ever run
- it only targets the latest batch
- Thunder resolves the files from the tracker table and runs `down()` on them
- migrations are processed in reverse order during rollback

This is why writing a correct `down()` method matters.
