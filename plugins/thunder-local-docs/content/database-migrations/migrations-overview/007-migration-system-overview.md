---
title: "Migration System Overview"
slug: "migration-system-overview"
description: "Introduction to Thunder migrations, how migration classes are structured, and how the schema builder workflow operates."
published: true
order: 7
source_id: 137
keywords: ["migration", "system", "overview", "introduction", "thunder", "migrations", "classes", "structured", "schema", "builder", "workflow", "operates", "database", "basic", "structure", "uses", "why", "timestamp", "prefix", "matters", "namespace", "requirement", "builder-based", "design"]
---

**Overview**

Thunder migrations are PHP classes used to define database schema changes in code. Each migration class extends the base `Migration` class and usually contains two methods:

- `up()` for applying the change
- `down()` for reversing the change

**Basic structure**

A generated migration follows this general pattern:

```php
namespace Migration;

class Create_posts_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addPrimaryKey('id');
        $this->createTable('posts');
    }

    public function down()
    {
        $this->dropTable('posts');
    }
}
```

**How Thunder uses migrations**

When you run:

```php
php thunder migrate blog
```

Thunder:

1. finds the plugin's migration files
2. sorts them by filename
3. resolves the migration class name from the filename
4. instantiates the class
5. calls `up()`
6. logs the migration in the migration tracker

When rolling back, Thunder calls `down()` and removes the migration record from the tracker.

**Why the timestamp prefix matters**

Migration filenames are generated with a timestamp prefix such as:

```php
2026-04-21_153000_create-posts-table.php
```

That naming strategy gives you deterministic execution order. Earlier files run first.

**Namespace requirement**

Thunder resolves migration classes under the `Migration` namespace. That means your migration file must define classes like:

```php
namespace Migration;
```

If the class is missing or the namespace is wrong, Thunder cannot resolve and execute the migration.

**Builder-based design**

The Migration class is not a raw SQL runner only. It works like a schema builder. You usually queue definitions first:

```php
$this->addColumn(...)
$this->addKey(...)
$this->addPrimaryKey(...)
```

and then execute the final table creation:

```php
$this->createTable('my_table');
```

This separation makes migrations easier to read and maintain.
