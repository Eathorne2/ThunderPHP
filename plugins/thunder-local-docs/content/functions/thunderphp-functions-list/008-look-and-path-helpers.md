---
title: "Look and Path Helpers"
slug: "look-and-path-helpers"
description: "Helpers for building filesystem and HTTP paths to plugin files, views, and assets."
published: true
order: 8
source_id: 124
keywords: ["look", "path", "helpers", "building", "filesystem", "http", "paths", "plugin", "files", "views", "assets", "functions", "thunderphp", "list", "covered", "current_look", "string", "current_look_http", "plugin_path", "plugin_http_path", "class_path", "folder", "class_name", "recipe"]
---

**Functions covered**

- `current_look()`
- `current_look_http()`
- `plugin_path()`
- `plugin_http_path()`
- `class_path()`

**`current_look(string $path)`**

Builds a filesystem path inside the current look folder of the current plugin.

```php
include current_look('frontend/home.php');
include current_look('admin/form.php');
```

This is the preferred way to include view files from the active look.

**`current_look_http(string $path)`**

Builds a public HTTP path inside the current look folder.

```php
echo current_look_http('assets/css/style.css');
echo current_look_http('assets/js/app.js');
```

Use this for CSS, JavaScript, images, and other public look assets.

**`plugin_path(string $path = '')`**

Builds a filesystem path relative to the current plugin folder.

```php
require plugin_path('controllers/admin/posts.php');
```

This is useful for PHP includes outside the look system.

**`plugin_http_path(string $path = '')`**

Builds a public HTTP path relative to the current plugin folder.

```php
echo plugin_http_path('assets/images/logo.png');
echo plugin_http_path('looks/main/assets/css/admin.css');
```

**`class_path(string $folder, string $class_name)`**

Builds the path to a class file from another plugin's `models` folder.

```php
require_once class_path('basic-blog', 'Post');
```

This should be a fallback, not the first choice. Prefer hooks for cross-plugin integration where possible.

**Recipe: include the current look header**

```php
include current_look('frontend/header.php');
```

**Recipe: load plugin-specific CSS**

```php
<link rel="stylesheet" href="<?= plugin_http_path('looks/main/assets/css/style.css') ?>">
```
