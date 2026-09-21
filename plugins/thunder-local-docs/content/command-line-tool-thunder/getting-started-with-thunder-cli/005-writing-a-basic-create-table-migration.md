---
title: "Writing a Basic Create Table Migration"
slug: "writing-a-basic-create-table-migration"
description: "A beginner example showing how to define columns, keys, and create a table using the Migration builder."
published: true
order: 5
source_id: 153
keywords: ["writing", "basic", "create", "table", "migration", "beginner", "example", "showing", "define", "columns", "keys", "builder", "command", "line", "tool", "thunder", "getting", "started", "cli", "step", "write", "happening", "here", "important"]
---

**Step 3: write the migration**

Thunder migrations use a builder-like style. You queue up columns, keys, and constraints, then call `createTable()`.

Here is a simple beginner example:

```php
<?php

namespace Migration;

defined('ROOTPATH') or die("Direct script access denied");

class Create_posts_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('title varchar(255) null');
        $this->addColumn('content text null');
        $this->addColumn('date_created datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('date_created');

        $this->createTable('posts');
    }

    public function down()
    {
        $this->dropTable('posts');
    }
}
```

**What is happening here**

- `addColumn()` queues column definitions
- `addPrimaryKey()` defines the primary key
- `addKey()` adds a normal index
- `createTable()` compiles and runs the final `CREATE TABLE` statement

**Important beginner idea**

Methods like `addColumn()` do not immediately create anything. They only build up the schema definition in memory. The actual table is only created when `createTable()` runs.
