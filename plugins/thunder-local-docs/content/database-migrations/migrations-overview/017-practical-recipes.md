---
title: "Practical Recipes"
slug: "practical-recipes"
description: "Real-world migration recipes for creating tables, adding indexes, adding foreign keys, and evolving schema safely."
published: true
order: 17
source_id: 147
keywords: ["practical", "recipes", "real-world", "migration", "creating", "tables", "adding", "indexes", "foreign", "keys", "evolving", "schema", "safely", "database", "migrations", "overview", "recipe", "create", "standard", "content", "table", "add", "column", "existing"]
---

**Recipe 1: Create a standard content table**

```php
namespace Migration;

class Create_posts_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('title varchar(255) not null');
        $this->addColumn('slug varchar(150) not null');
        $this->addColumn('body text null');
        $this->addColumn('deleted tinyint(1) unsigned default 0');
        $this->addColumn('date_created datetime default null');
        $this->addColumn('date_updated datetime default null');

        $this->addPrimaryKey('id');
        $this->addKey('user_id', 'idx_posts_user_id');
        $this->addUniqueKey('slug', 'uniq_posts_slug');

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

---

**Recipe 2: Add a column to an existing table**

```php
public function up()
{
    $this->addColumnToTable('posts', 'status varchar(50) default "draft"');
    $this->addIndexToTable('posts', 'status', 'idx_posts_status');
}

public function down()
{
    $this->dropIndex('posts', 'idx_posts_status');
    $this->dropColumn('posts', 'status');
}
```

---

**Recipe 3: Seed a default admin record**

```php
public function up()
{
    $this->addColumn('id int unsigned auto_increment');
    $this->addColumn('username varchar(100) not null');
    $this->addColumn('email varchar(255) not null');
    $this->addPrimaryKey('id');
    $this->createTable('admins');

    $this->addData([
        'username' => 'admin',
        'email'    => 'admin@example.com',
    ]);

    $this->insert('admins');
}

public function down()
{
    $this->dropTable('admins');
}
```

---

**Recipe 4: Rename and redefine a column**

```php
public function up()
{
    $this->renameColumn(
        'users',
        'fullname',
        'display_name',
        'display_name varchar(255) not null'
    );
}

public function down()
{
    $this->renameColumn(
        'users',
        'display_name',
        'fullname',
        'fullname varchar(255) not null'
    );
}
```

---

**Recipe 5: Development refresh loop**

```php
// Create the migration
php thunder make:migration blog create-posts-table

// Run it
php thunder migrate blog

// Adjust migration during early development only
// Then rebuild latest batch
php thunder migrate:refresh blog
```

Use refresh only when you understand that it rolls back the latest batch and runs it again.
