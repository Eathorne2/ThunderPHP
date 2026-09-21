---
title: "Using Foreign Keys, Unique Keys, and Fulltext Indexes"
slug: "using-foreign-keys-unique-keys-and-fulltext-indexes"
description: "A practical beginner example of adding more advanced table rules inside a migration."
published: true
order: 9
source_id: 157
keywords: ["foreign", "keys", "unique", "fulltext", "indexes", "practical", "beginner", "example", "adding", "advanced", "table", "rules", "inside", "migration", "command", "line", "tool", "thunder", "getting", "started", "cli", "columns"]
---

**Adding more than columns**

Thunder migrations can define more than just columns. You can also add:

- normal keys
- unique keys
- fulltext keys
- foreign keys

Here is a more complete example:

```php
<?php

namespace Migration;

defined('ROOTPATH') or die("Direct script access denied");

class Create_posts_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned default 0');
        $this->addColumn('title varchar(255) null');
        $this->addColumn('slug varchar(100) null');
        $this->addColumn('content text null');

        $this->addPrimaryKey('id');
        $this->addKey('user_id', 'idx_posts_user_id');
        $this->addUniqueKey('slug', 'uniq_posts_slug');
        $this->addFullTextKey('content');

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

    public function down()
    {
        $this->dropTable('posts');
    }
}
```

**What these rules do**

- `addKey()` improves lookup speed
- `addUniqueKey()` prevents duplicate values
- `addFullTextKey()` supports fulltext search where available
- `addForeignKey()` enforces table relationships

This is enough for many real plugin tables.
