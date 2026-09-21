---
title: "List Installed Plugins"
slug: "list-installed-plugins"
description: "Print all detected plugins and basic metadata from their config files."
published: true
order: 3
source_id: 165
keywords: ["list", "installed", "plugins", "print", "all", "detected", "basic", "metadata", "config", "files", "command", "line", "tool", "thunder", "cli", "recipes"]
---

Use this when you want to confirm that your plugins are being detected correctly.

```php
php thunder list:plugins
```

This is useful for checking:

- plugin name
- plugin ID
- plugin author
- plugin version
- whether the plugin is active or inactive

Run this before migrations if you want to confirm the plugin folder and `config.json` are in place.
