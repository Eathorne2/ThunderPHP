---
title: "Create a Table with a Foreign Key"
slug: "create-a-table-with-a-foreign-key"
description: "Create a table that references another table using a foreign key constraint."
published: true
order: 7
source_id: 169
keywords: ["create", "table", "foreign", "key", "references", "another", "constraint", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when your new table should belong to a record in another table.

```php
<?php

namespace Migration;

defined('ROOTPATH') or die("Direct script access denied");

class Create_comments_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('post_id int unsigned default 0');
        $this->addColumn('message text null');
        $this->addColumn('date_created datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('post_id', 'idx_comments_post_id');

        $this->addForeignKey(
            column: 'post_id',
            refTable: 'posts',
            refColumn: 'id',
            onDelete: 'CASCADE',
            onUpdate: 'CASCADE',
            name: 'fk_comments_post_id'
        );

        $this->createTable('comments');
    }

    public function down()
    {
        $this->dropTable('comments');
    }
}
```

Use named foreign keys so later changes and debugging are easier.
