---
title: "Render Frontend Views Through Looks"
slug: "render-frontend-views-through-looks"
description: "Output frontend templates using the current look system."
published: true
order: 8
source_id: 247
keywords: ["render", "frontend", "views", "through", "looks", "output", "templates", "current", "look", "system", "plugin", "dev", "tutorials", "build", "first"]
---

Register a `view` hook for output:

```php
add_action('view', function($data){
    include current_look('frontend/index.php');
}, 10, 'blog.index');

add_action('view', function($data){
    include current_look('frontend/view.php');
}, 10, 'blog.view');
```

This gives the plugin a clean separation between controller processing and final output. It also allows the plugin to support alternate looks later without rewriting business logic.
