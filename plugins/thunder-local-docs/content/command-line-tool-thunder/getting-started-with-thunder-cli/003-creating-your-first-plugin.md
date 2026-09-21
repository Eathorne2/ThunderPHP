---
title: "Creating Your First Plugin"
slug: "creating-your-first-plugin"
description: "Step-by-step beginner walkthrough for creating a new plugin with the Thunder CLI."
published: true
order: 3
source_id: 151
keywords: ["creating", "first", "plugin", "step-by-step", "beginner", "walkthrough", "new", "thunder", "cli", "command", "line", "tool", "getting", "started", "step", "create", "gets", "created", "already", "exists", "check", "next"]
---

**Step 1: create the plugin**

The first command most beginners use is `make:plugin`.

```php
php thunder make:plugin blog
```

This creates a new plugin folder with the expected ThunderPHP structure.

**What gets created**

The CLI creates the base folder and common subfolders such as:

```php
plugins/blog/controllers/
plugins/blog/models/
plugins/blog/migrations/
plugins/blog/looks/main/
plugins/blog/looks/main/assets/css/
plugins/blog/looks/main/assets/js/
plugins/blog/looks/main/assets/fonts/
plugins/blog/looks/main/assets/images/
```

It also attempts to copy stub files like `config.json`, plugin starter files, CSS, JavaScript, and basic view files.

**If the plugin already exists**

If the folder already exists, the CLI will stop unless you pass `--force`:

```php
php thunder make:plugin blog --force
```

Use `--force` carefully. It is best used when you intentionally want to replace scaffold files.

**What to check next**

After creating the plugin, check the folder structure and confirm the plugin was created where you expected.

A practical next step is to generate the model and migration you need for the plugin.
