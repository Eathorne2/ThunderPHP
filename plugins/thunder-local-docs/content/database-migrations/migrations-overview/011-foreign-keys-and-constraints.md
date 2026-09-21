---
title: "Foreign Keys and Constraints"
slug: "foreign-keys-and-constraints"
description: "How to define foreign keys during table creation and how to add or remove them on existing tables."
published: true
order: 11
source_id: 141
keywords: ["foreign", "keys", "constraints", "define", "during", "table", "creation", "add", "remove", "them", "existing", "tables", "database", "migrations", "overview", "method", "signature", "parameters", "example", "adding", "key", "dropping", "best", "practices"]
---

**Foreign keys during table creation**

Use `addForeignKey()` before `createTable()` when building a new table.

**Method signature**

```php
$this->addForeignKey(
    column: 'user_id',
    refTable: 'users',
    refColumn: 'id',
    onDelete: 'CASCADE',
    onUpdate: 'CASCADE',
    name: 'fk_posts_user_id'
);
```

**Parameters**

- `column` is the local column
- `refTable` is the referenced table
- `refColumn` is the referenced column, defaulting to `id`
- `onDelete` controls delete behavior
- `onUpdate` controls update behavior
- `name` optionally names the constraint

**Example**

```php
public function up()
{
    $this->addColumn('id int unsigned auto_increment');
    $this->addColumn('user_id int unsigned not null');
    $this->addColumn('title varchar(255) not null');

    $this->addPrimaryKey('id');
    $this->addKey('user_id', 'idx_posts_user_id');

    $this->addForeignKey(
        column: 'user_id',
        refTable: 'users',
        refColumn: 'id',
        onDelete: 'CASCADE',
        onUpdate: 'CASCADE',
        name: 'fk_posts_user_id'
    );

    $this->createTable('posts');
}
```

**Adding a foreign key to an existing table**

Use `addForeignKeyToTable()` when the table already exists.

```php
$this->addForeignKeyToTable(
    'posts',
    'user_id',
    'users',
    'id',
    'CASCADE',
    'CASCADE',
    'fk_posts_user_id'
);
```

**Dropping a foreign key**

Use `dropForeignKey()` with the constraint name:

```php
$this->dropForeignKey('posts', 'fk_posts_user_id');
```

**Best practices**

- ensure the referenced table exists first
- ensure the column types match
- add an index on the foreign key column where appropriate
- prefer explicit constraint names in production projects

**Practical rollback recipe**

```php
public function up()
{
    $this->addForeignKeyToTable(
        'invoices',
        'user_id',
        'users',
        'id',
        'CASCADE',
        'CASCADE',
        'fk_invoices_user_id'
    );
}

public function down()
{
    $this->dropForeignKey('invoices', 'fk_invoices_user_id');
}
```
