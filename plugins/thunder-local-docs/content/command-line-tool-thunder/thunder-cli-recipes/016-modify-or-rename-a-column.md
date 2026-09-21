---
title: "Modify or Rename a Column"
slug: "modify-or-rename-a-column"
description: "Change an existing column definition or rename a column safely through a new migration."
published: true
order: 16
source_id: 178
keywords: ["modify", "rename", "column", "change", "existing", "definition", "safely", "through", "new", "migration", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use `modifyColumn()` when the column name stays the same but the SQL definition changes.

```php
<?php

namespace Migration;

class Expand_post_title_length extends Migration
{
    public function up()
    {
        $this->modifyColumn('posts', 'title varchar(500) null');
    }

    public function down()
    {
        $this->modifyColumn('posts', 'title varchar(255) null');
    }
}
```

Use `renameColumn()` when the name changes too.

```php
<?php

namespace Migration;

class Rename_message_to_content extends Migration
{
    public function up()
    {
        $this->renameColumn('comments', 'message', 'content', 'content text null');
    }

    public function down()
    {
        $this->renameColumn('comments', 'content', 'message', 'message text null');
    }
}
```

Always mirror the change in `down()` when possible.
