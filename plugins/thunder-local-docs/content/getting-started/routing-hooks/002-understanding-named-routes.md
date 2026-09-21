---
title: "Understanding Named Routes"
slug: "understanding-named-routes"
description: "How named routes work and why they are preferred."
published: true
order: 2
source_id: 271
keywords: ["understanding", "named", "routes", "work", "why", "preferred", "getting", "started", "routing", "hooks"]
---

Named routes are defined in `config.json`:

```php
{
    "method":"GET",
    "pattern":"/blog/{slug}",
    "name":"blog.view"
}
```

They allow clean targeting:

```php
add_action('controller', function(){
    // logic
}, 10, 'blog.view');
```

Benefits:

- cleaner than URL segment checks
- easier to scale
- easier to debug
- works well with route parameters

Always prefer named routes for actual feature logic.
