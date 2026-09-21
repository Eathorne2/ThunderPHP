---
title: "Optional Next Step: Add a Migration"
slug: "optional-next-step-add-a-migration"
description: "See how even a small plugin can define its own tables when needed."
published: true
order: 8
source_id: 261
keywords: ["optional", "next", "step", "add", "migration", "see", "even", "small", "plugin", "define", "own", "tables", "needed", "getting", "started", "create", "first"]
---

If your first plugin needs a table, generate a migration:

```php
php thunder make:migration hello-world create_messages_table
```

Example migration:

```php
namespace Migration;

class Create_messages_table extends Migration
{
    public function up()
    {
        $this->addColumn("id INT UNSIGNED NOT NULL AUTO_INCREMENT");
        $this->addColumn("message VARCHAR(255) NOT NULL");
        $this->addPrimaryKey("id");
        $this->createTable("hello_world_messages");
    }

    public function down()
    {
        $this->dropTable("hello_world_messages");
    }
}
```

Then run it:

```php
php thunder migrate hello-world
```

This is one of the strengths of ThunderPHP: the plugin can own its own tables instead of depending on a fixed app schema.
