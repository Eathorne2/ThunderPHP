---
title: "The up() and down() Methods"
slug: "the-up-and-down-methods"
description: "Reference for the purpose and expected behavior of up and down methods in Thunder migrations."
published: true
order: 9
source_id: 139
keywords: ["up", "down", "methods", "reference", "purpose", "expected", "behavior", "thunder", "migrations", "database", "overview", "best", "practice", "perfect", "reversal", "possible", "practical", "recipe"]
---

**Purpose**

Every migration should define two core methods:

- `up()` applies the schema change
- `down()` reverses it

Together, these methods make migrations reversible.

**up()**

Use `up()` to create or modify database structures.

Examples of things you may do in `up()`:

- create a table
- add columns
- add indexes
- add foreign keys
- insert seed data

Example:

```php
public function up()
{
    $this->addColumn('id int unsigned auto_increment');
    $this->addColumn('title varchar(255) not null');
    $this->addPrimaryKey('id');
    $this->createTable('posts');
}
```

**down()**

Use `down()` to undo what `up()` did.

Example:

```php
public function down()
{
    $this->dropTable('posts');
}
```

If `up()` added a column to an existing table, then `down()` should normally drop that same column.

Example:

```php
public function up()
{
    $this->addColumnToTable('posts', 'status varchar(50) default "draft"');
}

public function down()
{
    $this->dropColumn('posts', 'status');
}
```

**Best practice**

Keep `down()` accurate. A migration system is much more useful when rollbacks truly reverse the change.

**When perfect reversal is not possible**

Sometimes destructive changes are difficult to restore exactly. For example, if `up()` drops a column with data in it, `down()` cannot realistically recover the lost data. In those cases:

- document the limitation in comments
- avoid destructive schema changes unless necessary
- back up data first in production environments

**Practical recipe**

```php
public function up()
{
    $this->addColumnToTable('users', 'phone varchar(30) null');
    $this->addIndexToTable('users', 'phone', 'idx_users_phone');
}

public function down()
{
    $this->dropIndex('users', 'idx_users_phone');
    $this->dropColumn('users', 'phone');
}
```

This gives you a clean reversible migration.
