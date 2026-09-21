---
title: "Route Parameters"
slug: "route-parameters"
description: "How to access dynamic parts of routes."
published: true
order: 3
source_id: 272
keywords: ["route", "parameters", "access", "dynamic", "parts", "routes", "getting", "started", "routing", "hooks"]
---

When using dynamic routes:

```php
"pattern":"/blog/{slug}"
```

You can access values using:

```php
$slug = get_param('slug');
```

This replaces manually parsing URLs and keeps your code consistent with route definitions.
