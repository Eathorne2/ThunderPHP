---
title: "Complete Everyday Workflow"
slug: "complete-everyday-workflow"
description: "A practical start-to-finish workflow for creating, checking, running, and rolling back migrations."
published: true
order: 18
source_id: 180
keywords: ["complete", "everyday", "workflow", "practical", "start-to-finish", "creating", "checking", "running", "rolling", "back", "migrations", "command", "line", "tool", "thunder", "cli", "recipes"]
---

This is a simple real-world workflow you can follow during plugin development.

Create a plugin:

```php
php thunder make:plugin blog
```

Create a migration:

```php
php thunder make:migration blog create_posts_table
```

Edit the migration file and write `up()` and `down()`.

Check current status:

```php
php thunder migrate:status blog
```

Run pending migrations:

```php
php thunder migrate blog
```

Confirm they were logged:

```php
php thunder migrate:status blog
```

If you need to undo the latest batch:

```php
php thunder migrate:rollback blog
```

If you want to quickly rebuild the latest set while developing:

```php
php thunder migrate:refresh blog
```

A safe habit is:

- create a new migration for every schema change
- never edit an old migration that already ran in shared environments
- always write a meaningful `down()` method
- use `migrate:status` before risky operations
