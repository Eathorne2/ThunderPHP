---
title: "Looks, Views, and Asset Paths"
slug: "looks-views-and-asset-paths"
description: "How ThunderPHP handles looks, templates, and look-specific asset loading."
published: true
order: 7
source_id: 230
keywords: ["looks", "views", "asset", "paths", "thunderphp", "handles", "templates", "look-specific", "loading", "plugin", "reference", "refrence", "developers", "filesystem", "path", "view", "http", "look", "assets", "best", "practice"]
---

A plugin can contain multiple looks. A look is a complete view set stored under the plugin's `looks/` directory.

The active look is selected through plugin metadata, and helper functions resolve the correct files.

**Filesystem Path to a View**

```php
include current_look('frontend/header.php');
```

`current_look()` returns the full filesystem path inside the currently active look folder.

**HTTP Path to Look Assets**

```php
<link rel="stylesheet" href="<?=current_look_http('assets/css/main.css')?>">
```

**HTTP Path to Plugin Assets**

```php
<img src="<?=plugin_http_path('assets/images/logo.png')?>" alt="">
```

**Best Practice**

Keep view files mostly focused on:

- loops
- simple `if` conditions
- output markup

Put business logic in controller hooks or included controller files, not in the view layer.
