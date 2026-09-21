---
title: "Create the Plugin"
slug: "create-the-plugin"
description: "Use the Thunder CLI to scaffold the plugin and inspect the generated structure."
published: true
order: 2
source_id: 255
keywords: ["create", "plugin", "thunder", "cli", "scaffold", "inspect", "generated", "structure", "getting", "started", "first"]
---

Start by generating the plugin:

```php
php thunder make:plugin hello-world
```

After generation, you should have a plugin folder with the recommended structure. At minimum, make sure these files exist:

- `plugin.php`
- `config.json`
- `looks/main/look.json`

The CLI-generated structure is recommended because it matches the normal ThunderPHP workflow and keeps plugins consistent.
