---
title: "Permissions and Role-Aware Admin Features"
slug: "permissions-and-role-aware-admin-features"
description: "Add capability checks so the plugin can be reused in different projects safely."
published: true
order: 13
source_id: 252
keywords: ["permissions", "role-aware", "admin", "features", "add", "capability", "checks", "plugin", "reused", "different", "projects", "safely", "dev", "tutorials", "build", "first"]
---

Register the plugin's permissions:

```php
add_filter('permissions', function($permissions){
    $permissions[] = 'view blog posts';
    $permissions[] = 'add blog post';
    $permissions[] = 'edit blog post';
    $permissions[] = 'delete blog post';
    return $permissions;
});
```

Then check them:

```php
if(!user_can('add blog post'))
{
    message('fail', 'You do not have permission');
    redirect('admin/blog');
}
```

This keeps the plugin reusable across many projects with different role and permission systems.
