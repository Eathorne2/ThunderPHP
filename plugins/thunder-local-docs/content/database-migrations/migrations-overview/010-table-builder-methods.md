---
title: "Table Builder Methods"
slug: "table-builder-methods"
description: "Comprehensive reference for createTable, addColumn, addKey, addPrimaryKey, addUniqueKey, addFullTextKey, and table settings."
published: true
order: 10
source_id: 140
keywords: ["table", "builder", "methods", "comprehensive", "reference", "createtable", "addcolumn", "addkey", "addprimarykey", "adduniquekey", "addfulltextkey", "settings", "database", "migrations", "overview", "string", "bool", "column", "void", "array", "primarykey", "name", "key", "complete"]
---

**Overview**

The base `Migration` class provides a queue-based builder for constructing new tables. You define columns, indexes, and constraints first, then call `createTable()` once.

**createTable(string $table): bool**

Creates the table using the definitions you have already added.

```php
$this->createTable('posts');
```

If no columns were added first, table creation fails.

---

**addColumn(string $column): void**

Adds a raw SQL column definition to the queue.

```php
$this->addColumn('id int unsigned auto_increment');
$this->addColumn('title varchar(255) not null');
$this->addColumn('content text null');
```

Because this method accepts raw SQL fragments, you have full flexibility, but you must provide valid SQL syntax yourself.

---

**addPrimaryKey(string|array $primaryKey, string $name = ''): void**

Adds a primary key definition.

Single-column example:

```php
$this->addPrimaryKey('id');
```

Composite example:

```php
$this->addPrimaryKey(['school_id', 'year', 'term']);
```

Optional named constraint:

```php
$this->addPrimaryKey('id', 'pk_posts_id');
```

---

**addKey(string|array $key, string $name = ''): void**

Adds a normal index.

```php
$this->addKey('date_created');
$this->addKey('user_id', 'idx_posts_user_id');
$this->addKey(['school_id', 'year'], 'idx_school_year');
```

---

**addUniqueKey(string|array $key, string $name = ''): void**

Adds a unique index.

```php
$this->addUniqueKey('slug', 'uniq_posts_slug');
$this->addUniqueKey(['school_id', 'code'], 'uniq_school_code');
```

---

**addFullTextKey(string|array $key, string $name = ''): void**

Adds a fulltext index.

```php
$this->addFullTextKey('body');
$this->addFullTextKey(['title', 'body'], 'ft_posts_search');
```

Use this when your storage engine and database version support fulltext indexing for the target columns.

---

**Table settings**

You can customize the table engine, charset, and collation before creation.

```php
$this->addEngine('InnoDB');
$this->addCharset('utf8mb4');
$this->addCollate('utf8mb4_general_ci');
```

These settings affect the generated `CREATE TABLE` statement.

---

**Complete example**

```php
public function up()
{
    $this->addColumn('id int unsigned auto_increment');
    $this->addColumn('user_id int unsigned default 0');
    $this->addColumn('title varchar(255) not null');
    $this->addColumn('slug varchar(150) not null');
    $this->addColumn('body text null');
    $this->addColumn('date_created datetime default null');

    $this->addPrimaryKey('id');
    $this->addKey('user_id', 'idx_posts_user_id');
    $this->addUniqueKey('slug', 'uniq_posts_slug');
    $this->addFullTextKey(['title', 'body'], 'ft_posts_search');

    $this->addEngine('InnoDB');
    $this->addCharset('utf8mb4');
    $this->addCollate('utf8mb4_general_ci');

    $this->createTable('posts');
}
```

**Important note**

The builder state is reset after a successful `createTable()`. That prevents definitions from leaking into the next table build inside the same migration.
