---
title: "Check Migration Status"
slug: "check-migration-status"
description: "See which migrations are already marked as ran and which are still pending."
published: true
order: 12
source_id: 174
keywords: ["check", "migration", "status", "see", "migrations", "already", "marked", "ran", "still", "pending", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to inspect migration progress.

For one plugin:

```php
php thunder migrate:status blog
```

For all plugins:

```php
php thunder migrate:status all
```

This helps you confirm:

- which files are already logged as `RAN`
- which files are still `PENDING`
- batch numbers for ran migrations
- the recorded run time for each migration

This is especially useful before rollbacks and refresh operations.
