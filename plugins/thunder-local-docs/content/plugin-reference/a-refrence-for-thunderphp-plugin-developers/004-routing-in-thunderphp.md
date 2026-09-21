---
title: "Routing in ThunderPHP"
slug: "routing-in-thunderphp"
description: "How coarse route loading and named clean routes work, and which approach is preferred."
published: true
order: 4
source_id: 227
keywords: ["routing", "thunderphp", "coarse", "route", "loading", "named", "clean", "routes", "work", "approach", "preferred", "plugin", "reference", "refrence", "developers", "off", "fine-grained", "prefer", "parameters"]
---

ThunderPHP supports two route-loading layers in plugins.

**1. Coarse Loading With `routes.on` and `routes.off`**

These affect plugin loading based on the first URL segment only.

For example, if `products` is in `routes.on`, the plugin loads for URLs that begin with `/products/...`.

This is simple and still supported.

**2. Fine-Grained Named Routes With `routes.routes`**

This is the cleaner and preferred approach.

Example:

```php
"routes":[
    {
        "method":"GET",
        "pattern":"/",
        "name":"home.index"
    },
    {
        "method":"GET",
        "pattern":"/post/{slug}",
        "name":"post.view"
    }
]
```

These named routes can then be used in hook registration or route checks.

```php
add_action('controller', function($data){
    // controller logic
}, 10, 'post.view');
```

Or:

```php
if(is_route('post.view'))
{
    // route-specific logic
}
```

**Which Should You Prefer?**

Both systems work, but `routes.routes` is preferred because it is cleaner, more explicit, and easier to reason about in larger plugins. `routes.on` and `routes.off` are still useful for broad load control.

**Route Parameters**

If a route contains placeholders such as `{slug}` or `{id}`, their values can be read with `get_param()`.

```php
$slug = get_param('slug');
```
