---
title: "Plugin Recipes and Common Pitfalls"
slug: "plugin-recipes-and-common-pitfalls"
description: "Practical patterns and the mistakes plugin authors should avoid."
published: true
order: 16
source_id: 239
keywords: ["plugin", "recipes", "common", "pitfalls", "practical", "patterns", "mistakes", "authors", "avoid", "reference", "refrence", "thunderphp", "developers", "recipe", "keep", "php", "small", "redirect", "after", "validation", "errors", "must", "read", "immediately"]
---

**Recipe: Keep `plugin.php` Small**

Put route setup and hook registration in `plugin.php`, but include files for large controller logic.

```php
add_action('controller', function($data){
    include plugin_path('controllers/frontend/posts.php');
}, 10, 'post.view');
```

**Recipe: Do Not Redirect After Validation If Errors Must Be Read Immediately**

Since `set_value()` is request-scoped and does not persist across redirects, validation errors intended for the same page should usually be handled without redirecting.

**Recipe: Prefer Named Routes**

Use `routes.routes` when possible. It scales better and makes route-scoped hooks cleaner.

**Pitfall: Accidental 404**

If you are building an API-like route and do not render a view, end the request after output, or the framework may continue into the 404 safeguard.

**Pitfall: Outdated Class Assumptions**

Older docs may understate what the newer Request, Image, Pager, Validate, and QueryBuilder layers can do. Base new plugin docs on the current class references instead of old summaries.
