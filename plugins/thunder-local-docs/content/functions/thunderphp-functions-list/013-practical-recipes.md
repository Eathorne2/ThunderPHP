---
title: "Practical Recipes"
slug: "practical-recipes"
description: "Common real-world patterns using the helper functions together in controllers, views, and plugin bootstrap code."
published: true
order: 13
source_id: 129
keywords: ["practical", "recipes", "common", "real-world", "patterns", "helper", "functions", "together", "controllers", "views", "plugin", "bootstrap", "code", "thunderphp", "list", "recipe", "basic", "values", "add", "admin", "sidebar", "link", "restrict", "controller"]
---

**Recipe 1: Basic plugin bootstrap values**

```php
set_value([
    'plugin_route' => 'blog',
    'admin_route'  => 'admin',
    'posts_table'  => 'bb_posts',
]);
```

This gives the rest of your plugin a predictable place to read route and table names.

**Recipe 2: Add an admin sidebar link**

```php
add_filter('basic-admin_before_admin_links', function($links) {
    $vars = get_value();

    $obj = (object)[];
    $obj->title  = 'Blog';
    $obj->link   = ROOT . '/' . $vars['admin_route'] . '/' . $vars['plugin_route'];
    $obj->icon   = 'fa-solid fa-file-lines';
    $obj->parent = 0;

    $links[] = $obj;
    return $links;
});
```

**Recipe 3: Restrict a controller action by permission**

```php
add_action('controller', function() {
    if(!user_can('edit post')) {
        message('fail', 'You do not have permission to do that');
        redirect('admin');
    }
}, 10, 'post.edit');
```

**Recipe 4: Render a route-specific view**

```php
add_action('view', function() {
    include current_look('frontend/post-view.php');
}, 10, 'post.view');
```

**Recipe 5: Use route parameters**

```php
add_action('controller', function() {
    $slug = get_param('slug');

    if(empty($slug)) {
        redirect('');
    }

    // load record by slug
}, 10, 'post.view');
```

**Recipe 6: Sticky form with CSRF and flash messages**

```php
<form method="post">
    <?= csrf() ?>

    <input type="text" name="title" value="<?= esc(old_value('title')) ?>">

    <select name="status">
        <option value="draft"<?= old_select('status', 'draft', 'draft') ?>>Draft</option>
        <option value="published"<?= old_select('status', 'published') ?>>Published</option>
    </select>

    <button type="submit">Save</button>
</form>
```

```php
add_action('controller', function() {
    if($_SERVER['REQUEST_METHOD'] != 'POST') {
        return;
    }

    if(!csrf_verify($_POST)) {
        message('fail', 'Invalid request');
        return;
    }

    message('success', 'Saved successfully');
});
```

**Recipe 7: Share an application-wide flag**

```php
APP_SET('maintenance_warning', true);

if(APP('maintenance_warning')) {
    // show warning banner
}
```

**Recipe 8: Guard optional plugin integration**

```php
if(plugin_exists('basic-admin')) {
    add_filter('basic-admin_before_admin_links', function($links) {
        // add links
        return $links;
    });
}
```
