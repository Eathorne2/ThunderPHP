---
title: "Core Helpers You Will Use Early"
slug: "core-helpers-you-will-use-early"
description: "The most important helpers a beginner plugin author should know first."
published: true
order: 6
source_id: 268
keywords: ["core", "helpers", "early", "most", "important", "beginner", "plugin", "author", "know", "first", "getting", "started", "request", "values", "current", "page", "load", "url", "redirects", "output", "safety", "messages", "csrf"]
---

Some helpers are used in almost every plugin.

**Request values for the current page load**

```php
set_value([
    'plugin_route' => 'blog',
    'admin_route' => 'admin'
]);

$vars = get_value();
```

**URL helpers**

```php
$page = page();
$segment1 = URL(1);
$current = current_url();
```

**Redirects**

```php
redirect('admin/blog');
```

**Output safety**

```php
echo esc($title);
```

**Messages**

```php
message('success', 'Post saved');
message('fail', 'Something went wrong');
```

**CSRF**

```php
<?=csrf()?>
```

Then verify posted data:

```php
csrf_verify($req->post());
```

These helpers form the everyday working surface of ThunderPHP plugin development.
