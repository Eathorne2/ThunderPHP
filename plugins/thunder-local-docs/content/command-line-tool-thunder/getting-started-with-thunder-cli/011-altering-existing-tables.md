---
title: "Altering Existing Tables"
slug: "altering-existing-tables"
description: "A simple introduction to add, modify, rename, and drop operations for existing tables."
published: true
order: 11
source_id: 159
keywords: ["altering", "existing", "tables", "simple", "introduction", "add", "modify", "rename", "drop", "operations", "command", "line", "tool", "thunder", "getting", "started", "cli", "migrations", "only", "new", "example", "column", "beginner", "reminder"]
---

**Migrations are not only for new tables**

Thunder's base `Migration` class also includes helpers for altering existing tables.

Available methods include:

- `addColumnToTable()`
- `modifyColumn()`
- `renameColumn()`
- `dropColumn()`
- `addForeignKeyToTable()`
- `dropForeignKey()`
- `addIndexToTable()`
- `addUniqueIndexToTable()`
- `dropIndex()`

**Example: add a new column**

```php
public function up()
{
    $this->addColumnToTable('posts', 'excerpt text null');
}

public function down()
{
    $this->dropColumn('posts', 'excerpt');
}
```

**Example: rename a column**

```php
public function up()
{
    $this->renameColumn('posts', 'body', 'content', 'content text null');
}
```

**Beginner reminder**

When changing existing tables, make sure your `down()` method really reverses the same change where possible.
