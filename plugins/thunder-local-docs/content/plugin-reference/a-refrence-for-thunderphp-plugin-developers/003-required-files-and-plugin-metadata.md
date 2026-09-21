---
title: "Required Files and Plugin Metadata"
slug: "required-files-and-plugin-metadata"
description: "Reference for plugin.php, config.json, look.json, and the main metadata rules."
published: true
order: 3
source_id: 226
keywords: ["required", "files", "plugin", "metadata", "reference", "php", "config", "json", "look", "main", "rules", "refrence", "thunderphp", "developers", "key"]
---

The main files expected in normal plugin development are:

- `plugin.php`
- `config.json`
- `look.json` inside each look folder

**plugin.php**

This is where the plugin registers hooks using `add_action()` and `add_filter()`.

**config.json**

This file defines metadata and route-loading rules.

Example:

```php
{
    "name": "Basic Blog",
    "version": "1.0.0",
    "core_requires": "^1.0.0",
    "id": "basic-blog",
    "description": "A basic but complete blogging system",
    "author": "Eathorne Choongo",
    "website": "thunderphp.com",
    "thumbnail": "thumbnail.jpg",
    "active": true,
    "look": "main",
    "index": 1,
    "routes": {
        "on": ["products", "admin", "home"],
        "off": ["login", "logout"],
        "routes":[
            {
                "method":"GET",
                "pattern":"/",
                "name":"home.index"
            },
            {
                "method":"GET",
                "pattern":"/post/{slug}",
                "name":"post.view"
            }
        ]
    },
    "dependencies": {
        "basic-admin":{
            "name":"Basic Admin",
            "version":"1.0.0",
            "required":false
        }
    }
}
```

**Key Metadata Rules**

- plugin `id` must be unique
- lower `index` values load earlier
- `core_requires` describes compatible framework versions
- `look` selects the active look folder
- `dependencies` can describe plugin requirements or optional integrations

**look.json**

Each look should define itself with a `look.json` file.

```php
{
    "name": "Dark Theme",
    "version": "1.0.0",
    "plugin": "plugin-id",
    "plugin_requires": "^1.2",
    "author": "Your Name",
    "thumbnail": "screenshot.png",
    "description": "A dark look for the plugin"
}
```

This helps keeps alternate looks explicit and easier to manage.
