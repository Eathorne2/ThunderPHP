---
title: "Creating Migration Files"
slug: "creating-migration-files"
description: "How migration files are generated, named, located, and resolved into executable classes."
published: true
order: 8
source_id: 138
keywords: ["creating", "migration", "files", "generated", "named", "located", "resolved", "executable", "classes", "database", "migrations", "overview", "generating", "file", "created", "automatic", "filename", "format", "class", "names", "derived", "required", "namespace", "example"]
---

**Generating a migration**

Use the Thunder CLI to create a migration file:

```php
php thunder make:migration blog create-posts-table
```

**Where the file is created**

The file is placed in the plugin's migrations folder:

```php
plugins/blog/migrations/
```

**Automatic filename format**

Thunder prefixes migration files with the current date and time:

```php
YYYY-MM-DD_His_name.php
```

Example:

```php
2026-04-21_153000_create-posts-table.php
```

This is important because Thunder later sorts migration files alphabetically, which also makes them run in chronological order.

**How class names are derived**

Thunder strips the timestamp prefix and converts the remaining name into a class name. It also:

- removes unsupported characters
- converts hyphens to underscores
- uppercases the first letter

So this file:

```php
2026-04-21_153000_create-posts-table.php
```

maps to a class name like:

```php
\Migration\Create_posts_table
```

**Required namespace**

Your migration class must live in the `Migration` namespace:

```php
namespace Migration;
```

**Example generated migration**

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

**Important rule**

The file name and the class name must remain compatible with Thunder's resolver. If you rename one carelessly without the other, Thunder may fail to load the migration.

**Practical tip**

Use descriptive migration names that explain the schema change clearly, such as:

```php
create-users-table
add-status-to-orders
create-post-comments-table
add-foreign-key-to-invoices
```

This makes migration history much easier to understand later.
