---
title: "Add a Controller File for Cleaner Logic"
slug: "add-a-controller-file-for-cleaner-logic"
description: "See how to move logic out of plugin.php once the plugin grows."
published: true
order: 7
source_id: 260
keywords: ["add", "controller", "file", "cleaner", "logic", "see", "move", "out", "plugin", "php", "once", "grows", "getting", "started", "create", "first"]
---

As soon as plugin logic starts getting larger, move it into a controller file.

Example:

```php
add_action('controller', function($data){
    include plugin_path('controllers/frontend/index.php');
}, 10, 'hello.index');
```

Then in `controllers/frontend/index.php`:

```php
<?php

$page_title = 'Welcome to my first plugin';
set_value('page_title', $page_title);
```

Later in the view:

```php
<h1><?=esc(get_value('page_title'))?></h1>
```

This keeps `plugin.php` cleaner and teaches a good long-term structure from the beginning.
