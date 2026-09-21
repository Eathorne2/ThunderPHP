---
title: "Creating Your First Migration File"
slug: "creating-your-first-migration-file"
description: "How to generate a migration file, understand the filename, and know what the generated stub contains."
published: true
order: 4
source_id: 152
keywords: ["creating", "first", "migration", "file", "generate", "understand", "filename", "know", "generated", "stub", "contains", "command", "line", "tool", "thunder", "getting", "started", "cli", "step", "create", "goes", "built", "why", "up"]
---

**Step 2: create a migration**

Once your plugin exists, create a migration file inside it:

```php
php thunder make:migration blog create-posts-table
```

**Where the file goes**

The file is created in:

```php
plugins/blog/migrations/
```

**How the filename is built**

Thunder prefixes the migration name with a timestamp, producing a filename like this:

```php
2026-04-21_123456_create-posts-table.php
```

This matters because migration files are sorted and run in filename order.

**What the generated stub contains**

The migration stub uses the `Migration` namespace and creates a class that extends the base `Migration` class. It includes two required methods:

```php
public function up()
{
    // apply schema changes
}

public function down()
{
    // reverse schema changes
}
```

**Why `up()` and `down()` matter**

- `up()` is for applying the migration
- `down()` is for reversing it

A good beginner rule is simple: whatever you create in `up()`, make sure `down()` can undo it cleanly.
