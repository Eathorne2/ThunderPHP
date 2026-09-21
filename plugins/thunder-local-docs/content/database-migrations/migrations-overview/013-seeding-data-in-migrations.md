---
title: "Seeding Data in Migrations"
slug: "seeding-data-in-migrations"
description: "How to queue and insert seed data using addData and insert within Thunder migrations."
published: true
order: 13
source_id: 143
keywords: ["seeding", "data", "migrations", "queue", "insert", "seed", "adddata", "within", "thunder", "database", "overview", "methods", "works", "example", "multiple", "rows", "internally", "best", "cases", "caution", "rollback", "note"]
---

**Overview**

Thunder migrations can also insert seed data. This is useful for default records such as:

- admin accounts
- default settings
- permission rows
- fixed lookup data

**Methods used**

- `addData(array $data): void`
- `insert(string $table): bool`

**How it works**

You call `addData()` once for each row you want to queue, then call `insert()` to write them to the table.

**Example**

```php
public function up()
{
    $this->addColumn('id int unsigned auto_increment');
    $this->addColumn('name varchar(255) not null');
    $this->addColumn('email varchar(255) not null');
    $this->addPrimaryKey('id');
    $this->createTable('admins');

    $this->addData([
        'name'  => 'Main Admin',
        'email' => 'admin@example.com',
    ]);

    $this->insert('admins');
}
```

**Multiple rows**

```php
$this->addData([
    'name' => 'Admin',
    'email' => 'admin@example.com',
]);

$this->addData([
    'name' => 'Editor',
    'email' => 'editor@example.com',
]);

$this->insert('admins');
```

**How insert works internally**

For each queued row, Thunder:

1. extracts the column names
2. builds a parameterized `INSERT INTO` query
3. binds the row values
4. executes the insert

This makes seed inserts cleaner than writing raw SQL manually each time.

**Best use cases**

Use migration seeding for data that must exist for the plugin to function correctly, such as:

- a seeded admin role
- default plugin settings
- required permission definitions

**Caution**

Avoid using migrations for large bulk imports. Migrations are best for schema changes and small essential seed data, not for massive content loading.

**Rollback note**

If you seed data in `up()`, think about whether `down()` should also delete it. In many simple cases, dropping the table is enough. But when adding data to existing tables, consider how to remove it safely in the rollback.
