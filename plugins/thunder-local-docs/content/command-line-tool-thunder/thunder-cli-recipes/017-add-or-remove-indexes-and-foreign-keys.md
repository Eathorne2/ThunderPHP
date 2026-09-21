---
title: "Add or Remove Indexes and Foreign Keys"
slug: "add-or-remove-indexes-and-foreign-keys"
description: "Manage indexes and foreign keys on existing tables using the alter-table helper methods."
published: true
order: 17
source_id: 179
keywords: ["add", "remove", "indexes", "foreign", "keys", "manage", "existing", "tables", "alter-table", "helper", "methods", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use these helper methods when the table already exists and you need to improve lookups or enforce relationships.

Add an index:

```php
<?php

namespace Migration;

class Add_index_to_posts_status extends Migration
{
    public function up()
    {
        $this->addIndexToTable('posts', 'status', 'idx_posts_status');
    }

    public function down()
    {
        $this->dropIndex('posts', 'idx_posts_status');
    }
}
```

Add a unique index:

```php
<?php

namespace Migration;

class Add_unique_index_to_roles_slug extends Migration
{
    public function up()
    {
        $this->addUniqueIndexToTable('roles', 'slug', 'uniq_roles_slug');
    }

    public function down()
    {
        $this->dropIndex('roles', 'uniq_roles_slug');
    }
}
```

Add a foreign key to an existing table:

```php
<?php

namespace Migration;

class Add_author_fk_to_posts extends Migration
{
    public function up()
    {
        $this->addForeignKeyToTable(
            table: 'posts',
            column: 'user_id',
            refTable: 'users',
            refColumn: 'id',
            onDelete: 'CASCADE',
            onUpdate: 'CASCADE',
            name: 'fk_posts_user_id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('posts', 'fk_posts_user_id');
    }
}
```

Name your indexes and foreign keys explicitly to make rollback easier.
