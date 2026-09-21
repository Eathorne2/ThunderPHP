---
title: "Seeding Data in a Migration"
slug: "seeding-data-in-a-migration"
description: "How to add starter rows during a migration using addData and insert."
published: true
order: 10
source_id: 158
keywords: ["seeding", "data", "migration", "add", "starter", "rows", "during", "adddata", "insert", "command", "line", "tool", "thunder", "getting", "started", "cli", "adding", "works", "good", "beginner", "cases"]
---

**Adding starter data**

Thunder migrations can also insert predefined rows. This is useful for things like:

- a default admin record
- starter settings
- default status values
- seed categories

The migration class lets you queue rows with `addData()` and then insert them with `insert()`.

Example:

```php
public function up()
{
    $this->addColumn('id int unsigned auto_increment');
    $this->addColumn('title varchar(100) null');
    $this->addPrimaryKey('id');
    $this->createTable('post_statuses');

    $this->addData([
        'title' => 'draft'
    ]);

    $this->addData([
        'title' => 'published'
    ]);

    $this->insert('post_statuses');
}
```

**How it works**

- every `addData()` call adds one row to an internal queue
- `insert()` loops through the queued rows and inserts them
- after insertion, the data queue is cleared

**Good beginner use cases**

Seeding is best for data that should exist immediately after the migration succeeds.
