---
title: "A Full Beginner Workflow Example"
slug: "a-full-beginner-workflow-example"
description: "A complete practical example showing plugin creation, migration creation, running, status checking, and rollback."
published: true
order: 13
source_id: 161
keywords: ["full", "beginner", "workflow", "example", "complete", "practical", "showing", "plugin", "creation", "migration", "running", "status", "checking", "rollback", "command", "line", "tool", "thunder", "getting", "started", "cli", "putting", "all", "together"]
---

**Putting it all together**

Here is a simple beginner workflow for a new plugin named `blog`.

**1. Create the plugin**

```php
php thunder make:plugin blog
```

**2. Create the migration**

```php
php thunder make:migration blog create-posts-table
```

**3. Edit the generated migration file**

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
        $this->createTable('posts');
    }

    public function down()
    {
        $this->dropTable('posts');
    }
}
```

**4. Check current migration status**

```php
php thunder migrate:status blog
```

**5. Run pending migrations**

```php
php thunder migrate blog
```

**6. Check status again**

```php
php thunder migrate:status blog
```

**7. Roll back if needed**

```php
php thunder migrate:rollback blog
```

**8. Re-run after fixes**

```php
php thunder migrate blog
```

This cycle is the core of migration-driven schema development in ThunderPHP.
