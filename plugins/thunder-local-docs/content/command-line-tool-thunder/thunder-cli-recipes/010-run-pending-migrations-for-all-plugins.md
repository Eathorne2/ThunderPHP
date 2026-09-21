---
title: "Run Pending Migrations for All Plugins"
slug: "run-pending-migrations-for-all-plugins"
description: "Execute all pending migrations across every plugin."
published: true
order: 10
source_id: 172
keywords: ["run", "pending", "migrations", "all", "plugins", "execute", "across", "every", "plugin", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to process pending migrations across the full project.

```php
php thunder migrate all
```

This is useful when:

- setting up a full project locally
- deploying a new version with several plugin updates
- testing whether all plugins migrate cleanly together

Because this runs across all plugin folders, it is best used when you are confident the plugin set is stable.
