---
title: "Run Pending Migrations for One Plugin"
slug: "run-pending-migrations-for-one-plugin"
description: "Execute all pending migration files for a specific plugin."
published: true
order: 9
source_id: 171
keywords: ["run", "pending", "migrations", "one", "plugin", "execute", "all", "migration", "files", "specific", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to migrate one plugin only.

```php
php thunder migrate blog
```

Thunder will:

- find the `migrations/` folder for that plugin
- sort migration files by filename
- skip files that have already been logged as run
- execute `up()` on pending files
- record successful runs in the migration tracker table

This is the safest everyday migration command when working on one plugin at a time.
