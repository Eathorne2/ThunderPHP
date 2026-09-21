---
title: "Roles, Permissions, and Access Checks"
slug: "roles-permissions-and-access-checks"
description: "How plugins define permissions and role-aware behavior."
published: true
order: 14
source_id: 237
keywords: ["roles", "permissions", "access", "checks", "plugins", "define", "role-aware", "behavior", "plugin", "reference", "refrence", "thunderphp", "developers", "practical", "advice"]
---

ThunderPHP expects plugins to register possible roles and permissions through filters.

Example permissions filter:

```php
add_filter('permissions', function($permissions){
    $permissions[] = 'view posts';
    $permissions[] = 'add post';
    $permissions[] = 'edit post';
    $permissions[] = 'delete post';
    return $permissions;
});
```

Then in views or controllers:

```php
if(user_can('add post'))
{
    // allow action
}
```

`user_can()` caches permission loading per page load and supports an `all` permission for universal access. Role helpers such as `user_roles()` and `contains_role()` provide similar convenience for role-based systems.

**Practical Advice**

- use roles for broad identity grouping
- use permissions for capability checks
- prefer permission checks in views and controllers instead of hardcoding role names everywhere
