---
title: "Create and Run the Migration"
slug: "create-and-run-the-migration"
description: "Build the posts table and run it through the Thunder migration system."
published: true
order: 5
source_id: 244
keywords: ["create", "run", "migration", "build", "posts", "table", "through", "thunder", "system", "plugin", "dev", "tutorials", "first"]
---

Generate a migration:

```php
php thunder make:migration basic-blog create_posts_table
```

Inside the migration:

```php
namespace Migration;

class Create_posts_table extends Migration
{
    public function up()
    {
        $this->addColumn("id INT UNSIGNED NOT NULL AUTO_INCREMENT");
        $this->addColumn("title VARCHAR(255) NOT NULL");
        $this->addColumn("slug VARCHAR(255) NOT NULL");
        $this->addColumn("content LONGTEXT NULL");
        $this->addColumn("featured_image VARCHAR(255) NULL");
        $this->addColumn("published TINYINT(1) NOT NULL DEFAULT 0");
        $this->addColumn("date_created DATETIME NULL");

        $this->addPrimaryKey("id");
        $this->addUniqueKey("slug", "uniq_blog_slug");

        $this->createTable("basic_blog_posts");
    }

    public function down()
    {
        $this->dropTable("basic_blog_posts");
    }
}
```

Then run:

```php
php thunder migrate basic-blog
```

The migration system tracks runs per plugin, supports batch rollback, and resolves classes from timestamped filenames.
