---
title: "Write a Basic Create Table Migration"
slug: "write-a-basic-create-table-migration"
description: "Create a table with columns, indexes, a primary key, and then build the table."
published: true
order: 6
source_id: 168
keywords: ["write", "basic", "create", "table", "migration", "columns", "indexes", "primary", "key", "build", "command", "line", "tool", "thunder", "cli", "recipes"]
---

After generating a migration file, open it and define the `up()` and `down()` methods.

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
        $this->addColumn('slug varchar(120) null');
        $this->addColumn('date_created datetime default null');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('slug', 'uniq_posts_slug');
        $this->addKey('date_created');

        $this->createTable('posts');
    }

    public function down()
    {
        $this->dropTable('posts');
    }
}
```

Use this pattern whenever you need to create a brand new table from scratch.
