---
title: "Avoiding the 404 Trap"
slug: "avoiding-the-404-trap"
description: "Common mistake with route-specific view hooks."
published: true
order: 6
source_id: 275
keywords: ["avoiding", "trap", "common", "mistake", "route-specific", "view", "hooks", "getting", "started", "routing"]
---

Wrong approach:

```php
add_action('view', fn()=>..., 10, 'blog.index');
add_action('view', fn()=>..., 10, 'blog.view');
```

If route mismatch → 404.

Correct approach:

```php
// layout
add_action('view', function(){
    include current_look('layout.php');
});

// route content
add_action('view', function(){
    include current_look('post.php');
}, 10, 'blog.view');
```

This guarantees rendering always happens.
