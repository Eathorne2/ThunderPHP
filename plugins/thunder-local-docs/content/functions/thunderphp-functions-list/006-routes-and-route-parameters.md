---
title: "Routes and Route Parameters"
slug: "routes-and-route-parameters"
description: "Functions for checking route matches, reading the current route name, and accessing named route parameters."
published: true
order: 6
source_id: 122
keywords: ["routes", "route", "parameters", "functions", "checking", "matches", "reading", "current", "name", "accessing", "named", "thunderphp", "list", "covered", "is_route", "string", "get_route_name", "get_param", "key", "example", "definition", "recipe", "load", "record"]
---

**Functions covered**

- `is_route()`
- `get_route_name()`
- `get_param()`

ThunderPHP supports named routes defined in `config.json`. Hook callbacks may optionally be bound to a specific route name.

**`is_route(string $route = '')`**

Checks whether the current plugin matched a particular named route.

```php
if(is_route('post.view')) {
    // post details page
}
```

If no route is supplied, the function returns `true`.

This makes route-restricted action and filter handlers easier to reuse.

**`get_route_name()`**

Returns the current matched route name for the current plugin.

```php
$route = get_route_name();
```

Useful for debugging or when one callback serves multiple routes.

**`get_param(string $key = '')`**

Returns a named route parameter extracted from a route pattern like `/post/{slug}`.

```php
$slug = get_param('slug');
$id   = get_param('id');
```

**Example route definition**

```php
{
    "method":"GET",
    "pattern":"/post/{slug}",
    "name":"post.view"
}
```

**Recipe: load a record from a route slug**

```php
add_action('controller', function() {
    if(!is_route('post.view')) {
        return;
    }

    $slug = get_param('slug');

    // use $slug to load the post
}, 10, 'post.view');
```

**Recipe: reuse one callback on more than one route**

```php
add_action('controller', function() {
    $route = get_route_name();

    if($route == 'post.add') {
        // add logic
    }

    if($route == 'post.edit') {
        // edit logic
    }
});
```
