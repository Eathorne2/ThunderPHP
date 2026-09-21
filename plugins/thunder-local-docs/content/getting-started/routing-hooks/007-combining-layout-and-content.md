---
title: "Combining Layout and Content"
slug: "combining-layout-and-content"
description: "Best practice pattern for real plugins."
published: true
order: 7
source_id: 276
keywords: ["combining", "layout", "content", "best", "practice", "pattern", "real", "plugins", "getting", "started", "routing", "hooks"]
---

Recommended pattern:

```php
// global layout
add_action('view', function(){
    include current_look('frontend/layout.php');
});

// route content
add_action('view', function(){
    include current_look('frontend/index.php');
}, 10, 'blog.index');
```

This mimics layout + page structure found in MVC frameworks but keeps flexibility of hooks.
