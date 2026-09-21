---
title: "Define Config and Routes"
slug: "define-config-and-routes"
description: "Set up plugin metadata, activation, load order, and preferred named routes."
published: true
order: 3
source_id: 242
keywords: ["define", "config", "routes", "set", "up", "plugin", "metadata", "activation", "load", "order", "preferred", "named", "dev", "tutorials", "build", "first"]
---

Create `config.json` for the plugin.

Example:

```php
{
    "name": "Basic Blog",
    "version": "1.0.0",
    "core_requires": "^1.0.0",
    "id": "basic-blog",
    "description": "A reusable blog plugin",
    "author": "Your Name",
    "website": "example.com",
    "thumbnail": "thumbnail.jpg",
    "active": true,
    "look": "main",
    "index": 1,
    "routes": {
        "on": ["blog", "admin"],
        "off": [],
        "routes":[
            {
                "method":"GET",
                "pattern":"/blog",
                "name":"blog.index"
            },
            {
                "method":"GET",
                "pattern":"/blog/{slug}",
                "name":"blog.view"
            },
            {
                "method":"GET",
                "pattern":"/admin/blog",
                "name":"blog.admin.index"
            }
        ]
    }
}
```

Use named routes wherever possible. They make later hook registration much cleaner than relying only on broad first-segment loading.
