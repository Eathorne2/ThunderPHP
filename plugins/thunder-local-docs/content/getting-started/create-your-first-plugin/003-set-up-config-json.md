---
title: "Set Up config.json"
slug: "set-up-config-json"
description: "Define the plugin metadata and the named routes for the first plugin."
published: true
order: 3
source_id: 256
keywords: ["set", "up", "config", "json", "define", "plugin", "metadata", "named", "routes", "first", "getting", "started", "create"]
---

Use a simple `config.json` like this:

```php
{
    "name": "Hello World",
    "version": "1.0.0",
    "core_requires": "^1.0.0",
    "id": "hello-world",
    "description": "My first ThunderPHP plugin",
    "author": "Your Name",
    "website": "",
    "thumbnail": "thumbnail.jpg",
    "active": true,
    "look": "main",
    "index": 1,
    "routes": {
        "on": ["hello", "admin"],
        "off": [],
        "routes":[
            {
                "method":"GET",
                "pattern":"/hello",
                "name":"hello.index"
            },
            {
                "method":"GET",
                "pattern":"/admin/hello",
                "name":"hello.admin"
            }
        ]
    }
}
```

This gives the plugin one frontend page and one admin page, both with clear route names.
