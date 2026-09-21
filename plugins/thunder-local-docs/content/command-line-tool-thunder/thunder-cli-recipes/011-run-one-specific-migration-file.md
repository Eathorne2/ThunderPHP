---
title: "Run One Specific Migration File"
slug: "run-one-specific-migration-file"
description: "Execute a single migration file if it has not already been run."
published: true
order: 11
source_id: 173
keywords: ["run", "one", "specific", "migration", "file", "execute", "single", "already", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to run just one file instead of every pending migration in the plugin.

```php
php thunder migrate blog 2026-04-21_103015_create_posts_table.php
```

Use this when:

- you are testing one new migration
- you want to isolate an error
- you want very controlled migration execution during development

The file must exist inside the plugin's `migrations/` folder.
