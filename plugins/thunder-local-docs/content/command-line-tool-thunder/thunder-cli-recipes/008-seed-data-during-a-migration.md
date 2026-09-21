---
title: "Seed Data During a Migration"
slug: "seed-data-during-a-migration"
description: "Insert starter rows after creating a table."
published: true
order: 8
source_id: 170
keywords: ["seed", "data", "during", "migration", "insert", "starter", "rows", "after", "creating", "table", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to insert default rows immediately after creating a table.

```php
<?php

namespace Migration;

defined('ROOTPATH') or die("Direct script access denied");

class Create_roles_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('name varchar(100) null');
        $this->addColumn('slug varchar(100) null');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('slug', 'uniq_roles_slug');

        $this->createTable('roles');

        $this->addData([
            'name' => 'Administrator',
            'slug' => 'admin',
        ]);

        $this->addData([
            'name' => 'User',
            'slug' => 'user',
        ]);

        $this->insert('roles');
    }

    public function down()
    {
        $this->dropTable('roles');
    }
}
```

Use this for default roles, settings, starter categories, or required base rows.
