---
title: "Admin Hooks Usage"
slug: "admin-hooks-usage"
description: "Correct usage of admin hooks in ThunderPHP."
published: true
order: 8
source_id: 277
keywords: ["admin", "hooks", "usage", "correct", "thunderphp", "getting", "started", "routing"]
---

Standard admin hooks:

```php
add_filter('admin_before_links', fn($links)=>$links);
add_action('admin_main_content', fn()=>...);
```

These should NOT be prefixed.

Custom hooks should follow:

```
pluginid_hookname
```

Example:

```php
add_action('blog_after_post', fn()=>...);
```

This avoids collisions between plugins.
