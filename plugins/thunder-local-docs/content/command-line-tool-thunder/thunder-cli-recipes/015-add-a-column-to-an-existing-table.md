---
title: "Add a Column to an Existing Table"
slug: "add-a-column-to-an-existing-table"
description: "Use alter-table helpers to append a new column without recreating the table."
published: true
order: 15
source_id: 177
keywords: ["add", "column", "existing", "table", "alter-table", "helpers", "append", "new", "without", "recreating", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when a table already exists and you want to add a new field.

```php
<?php

namespace Migration;

defined('ROOTPATH') or die("Direct script access denied");

class Add_status_to_posts extends Migration
{
    public function up()
    {
        $this->addColumnToTable('posts', 'status varchar(50) default "draft"');
    }

    public function down()
    {
        $this->dropColumn('posts', 'status');
    }
}
```

Use a dedicated migration like this instead of editing an old migration that has already been run in other environments.
