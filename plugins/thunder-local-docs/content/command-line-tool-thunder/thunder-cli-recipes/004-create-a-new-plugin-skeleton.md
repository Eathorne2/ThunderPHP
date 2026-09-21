---
title: "Create a New Plugin Skeleton"
slug: "create-a-new-plugin-skeleton"
description: "Generate a new plugin folder with the default Thunder structure and starter files."
published: true
order: 4
source_id: 166
keywords: ["create", "new", "plugin", "skeleton", "generate", "folder", "default", "thunder", "structure", "starter", "files", "command", "line", "tool", "cli", "recipes"]
---

Use this to scaffold a new plugin quickly.

```php
php thunder make:plugin blog
```

This creates the plugin folder and starter structure for common plugin development.

If the plugin already exists and you intentionally want to overwrite the scaffolded files, use:

```php
php thunder make:plugin blog --force
```

Use this recipe when starting a fresh plugin before creating models, controllers, migrations, or views.
