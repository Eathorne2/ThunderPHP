---
title: "What a Plugin Is"
slug: "what-a-plugin-is"
description: "Understand the job of plugin.php, config.json, looks, migrations, models, and controllers."
published: true
order: 2
source_id: 264
keywords: ["plugin", "understand", "job", "php", "config", "json", "looks", "migrations", "models", "controllers", "getting", "started", "core", "files"]
---

In ThunderPHP, a plugin is a self-contained feature module.

A plugin normally contains:

- `plugin.php`
- `config.json`
- `looks/`
- `migrations/`
- `models/`
- `controllers/`

**The Core Files**

`plugin.php` is where the plugin registers actions and filters.

`config.json` describes the plugin, its routes, whether it is active, and what look it uses.

`looks/` contains templates and assets for output.

`migrations/` contains schema changes for the plugin's tables.

`models/` contains plugin-specific data classes.

`controllers/` can contain included controller files to keep `plugin.php` small.

None of this is meant to trap the developer into a rigid structure, but following it keeps the plugin easier to understand and matches how the CLI tool scaffolds files.
