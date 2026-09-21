---
title: "Views, Looks, and the 404 Safeguard"
slug: "views-looks-and-the-404-safeguard"
description: "How view output works and why missing views can trigger 404 handling."
published: true
order: 5
source_id: 267
keywords: ["views", "looks", "safeguard", "view", "output", "works", "why", "missing", "trigger", "handling", "getting", "started", "important", "framework"]
---

A plugin can have one or more looks. Each look contains view files and assets.

To render a view from the active look:

```php
include current_look('frontend/index.php');
```

To load assets from the active look:

```php
<link rel="stylesheet" href="<?=current_look_http('assets/css/style.css')?>">
```

**Important Framework Safeguard**

If no view hook runs for a route that expects HTML, ThunderPHP treats that as unresolved and triggers the 404 flow. The framework also provides `do_action('before_404_redirect')` as an interception point before 404 handling continues.

That means:

- HTML routes should normally render through a view hook
- JSON or file-output routes should end the request deliberately with `die()` or `exit()`
