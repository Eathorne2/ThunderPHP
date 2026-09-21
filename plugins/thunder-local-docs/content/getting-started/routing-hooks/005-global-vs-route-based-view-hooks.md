---
title: "Global vs Route-Based View Hooks"
slug: "global-vs-route-based-view-hooks"
description: "Understand how view hooks behave globally and per-route."
published: true
order: 5
source_id: 274
keywords: ["global", "vs", "route-based", "view", "hooks", "understand", "behave", "globally", "per-route", "getting", "started", "routing", "hook", "route-specific", "important", "rule"]
---

**Global View Hook**

```php
add_action('view', function(){
    include current_look('layout.php');
});
```

Runs on ALL matched routes in that plugin.

**Route-Specific View Hook**

```php
add_action('view', function(){
    include current_look('post.php');
}, 10, 'blog.view');
```

Runs ONLY for that route.

**Important Rule**

A 404 happens ONLY when:

- all view hooks are route-specific
- none match the current route

If at least one global view exists → no 404.

This is critical to understand early.
