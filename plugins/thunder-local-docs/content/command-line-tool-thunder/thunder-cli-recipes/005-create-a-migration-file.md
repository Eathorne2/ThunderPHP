---
title: "Create a Migration File"
slug: "create-a-migration-file"
description: "Generate a timestamped migration file inside a plugin migrations folder."
published: true
order: 5
source_id: 167
keywords: ["create", "migration", "file", "generate", "timestamped", "inside", "plugin", "migrations", "folder", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to create a new migration file for a plugin.

```php
php thunder make:migration blog create_posts_table
```

Thunder creates the file inside the plugin's `migrations/` folder using a timestamped filename.

Example output filename:

```php
plugins/blog/migrations/2026-04-21_103015_create_posts_table.php
```

This timestamp prefix is important because migrations are sorted and executed in filename order.

A good naming style is:

- `create_posts_table`
- `add_status_to_posts`
- `add_author_foreign_key`
- `create_comments_table`
