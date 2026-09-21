---
title: "Create the Plugin Skeleton"
slug: "create-the-plugin-skeleton"
description: "How to start with the CLI and the recommended folder structure."
published: true
order: 2
source_id: 241
keywords: ["create", "plugin", "skeleton", "start", "cli", "recommended", "folder", "structure", "dev", "tutorials", "build", "first"]
---

Start with the CLI:

```php
php thunder make:plugin basic-blog
```

This gives you a recommended structure that already fits ThunderPHP's normal plugin workflow.

After creation, make sure the plugin has:

- `plugin.php`
- `config.json`
- `looks/main/look.json`
- migration, model, controller, and asset folders as needed

ThunderPHP allows flexible layouts, but sticking to the generated structure keeps the plugin easier to maintain and aligns it with CLI expectations.
