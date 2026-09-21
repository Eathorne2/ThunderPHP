---
title: "Altering Existing Tables"
slug: "altering-existing-tables"
description: "Reference for schema changes on existing tables, including adding, modifying, renaming, and dropping columns and indexes."
published: true
order: 12
source_id: 142
keywords: ["altering", "existing", "tables", "reference", "schema", "changes", "including", "adding", "modifying", "renaming", "dropping", "columns", "indexes", "database", "migrations", "overview", "addcolumntotable", "string", "table", "columnsql", "bool", "modifycolumn", "renamecolumn", "oldname"]
---

**Overview**

Thunder migrations are not limited to creating new tables. The base Migration class also includes helper methods for altering existing tables.

---

**addColumnToTable(string $table, string $columnSql): bool**

Adds a new column to an existing table.

```php
$this->addColumnToTable('users', 'phone varchar(30) null');
```

---

**modifyColumn(string $table, string $columnSql): bool**

Changes the definition of an existing column.

```php
$this->modifyColumn('users', 'phone varchar(50) null');
```

Use this when adjusting length, nullability, defaults, or data type.

---

**renameColumn(string $table, string $oldName, string $newName, string $newDefinition): bool**

Renames a column and defines its new structure at the same time.

```php
$this->renameColumn(
    'users',
    'fullname',
    'display_name',
    'display_name varchar(255) not null'
);
```

---

**dropColumn(string $table, string $column): bool**

Removes a column from an existing table.

```php
$this->dropColumn('users', 'phone');
```

---

**addIndexToTable(string $table, string|array $columns, string $name = ''): bool**

Adds a regular index to an existing table.

```php
$this->addIndexToTable('users', 'email', 'idx_users_email');
$this->addIndexToTable('results', ['school_id', 'year'], 'idx_results_school_year');
```

---

**addUniqueIndexToTable(string $table, string|array $columns, string $name = ''): bool**

Adds a unique index to an existing table.

```php
$this->addUniqueIndexToTable('users', 'email', 'uniq_users_email');
```

---

**dropIndex(string $table, string $name): bool**

Drops an existing index by name.

```php
$this->dropIndex('users', 'idx_users_email');
```

---

**Practical example**

```php
public function up()
{
    $this->addColumnToTable('posts', 'status varchar(50) default "draft"');
    $this->addIndexToTable('posts', 'status', 'idx_posts_status');
}

public function down()
{
    $this->dropIndex('posts', 'idx_posts_status');
    $this->dropColumn('posts', 'status');
}
```

**Best practice**

When altering existing tables:

- add indexes after the target columns exist
- drop indexes before dropping their columns in rollbacks
- keep `up()` and `down()` symmetrical whenever possible
