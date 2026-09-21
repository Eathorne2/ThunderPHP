---
title: "Refresh a Plugin Migration Set"
slug: "refresh-a-plugin-migration-set"
description: "Rollback the latest batch and then run migrate again for the plugin."
published: true
order: 14
source_id: 176
keywords: ["refresh", "plugin", "migration", "set", "rollback", "latest", "batch", "run", "migrate", "again", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to rebuild the latest migration state for a plugin.

```php
php thunder migrate:refresh blog
```

You can also target a specific file:

```php
php thunder migrate:refresh blog 2026-04-21_103015_create_posts_table.php
```

This command effectively performs:

```php
php thunder migrate:rollback blog
php thunder migrate blog
```

This is useful during development when you are still adjusting the schema and want to quickly rerun the latest migration set.
