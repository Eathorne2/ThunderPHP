---
title: "Roles and Permissions"
slug: "roles-and-permissions"
description: "Helpers for resolving current user roles and permissions and enforcing access in controllers and views."
published: true
order: 9
source_id: 125
keywords: ["roles", "permissions", "helpers", "resolving", "current", "user", "enforcing", "access", "controllers", "views", "functions", "thunderphp", "list", "covered", "user_roles", "contains_role", "string", "role", "user_can", "permission", "recipe", "protect", "admin", "page"]
---

**Functions covered**

- `user_roles()`
- `contains_role()`
- `user_can()`

ThunderPHP separates roles from permissions.

- Roles are broad labels such as `admin`, `editor`, or `user`.
- Permissions are specific capabilities such as `edit post` or `delete post`.

**`user_roles()`**

Returns all roles for the current user. The `user_roles` filter is evaluated once and cached for the rest of the request.

```php
$roles = user_roles();
```

Typical role registration:

```php
add_filter('user_roles', function($roles) {
    $ses = new \Core\Session;

    if($ses->is_logged_in()) {
        $roles[] = 'user';
    }

    return $roles;
});
```

**`contains_role(string $role)`**

Checks whether the current user has a role. Comparison is case-insensitive.

```php
if(contains_role('admin')) {
    // admin only
}
```

**`user_can(?string $permission)`**

Checks whether the current user has a permission.

```php
if(user_can('edit post')) {
    // allow editing
}
```

Special built-in checks:

- `user_can('logged_in')`
- `user_can('not_logged_in')`

Admin users automatically pass all permission checks because the helper returns `true` when `admin` is present in the role list.

The permission list is loaded from the `user_permissions` filter and cached for the request.

```php
add_filter('user_permissions', function($permissions) {
    $permissions[] = 'view posts';
    $permissions[] = 'edit post';
    return $permissions;
});
```

The special permission `all` grants universal permission access.

**Recipe: protect an admin page**

```php
add_action('controller', function() {
    if(!user_can('edit post')) {
        message('fail', 'You do not have permission to edit posts');
        redirect('admin');
    }
}, 10, 'post.edit');
```

**Recipe: hide a button in a view**

```php
<?php if(user_can('add post')): ?>
    <a href="<?= ROOT ?>/admin/blog/add">Add Post</a>
<?php endif; ?>
```
