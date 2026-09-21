---
title: "make:plugin"
slug: "make-plugin"
description: "How to generate a new plugin skeleton with Thunder, including folder structure, stubs, and the force overwrite option."
published: true
order: 3
source_id: 133
keywords: ["make", "plugin", "generate", "new", "skeleton", "thunder", "including", "folder", "structure", "stubs", "force", "overwrite", "option", "command", "line", "tool", "cli", "usage", "commands", "purpose", "gets", "created", "starter", "files"]
---

**Purpose**

The `make:plugin` command creates a new plugin folder with the standard ThunderPHP structure and starter files.

**Usage**

```php
php thunder make:plugin blog
```

To overwrite an existing plugin folder:

```php
php thunder make:plugin blog --force
```

**What gets created**

The command creates the plugin base directory and common subfolders, including:

```php
plugins/blog/
plugins/blog/controllers/
plugins/blog/models/
plugins/blog/migrations/
plugins/blog/looks/
plugins/blog/looks/main/
plugins/blog/looks/main/assets/css/
plugins/blog/looks/main/assets/js/
plugins/blog/looks/main/assets/fonts/
plugins/blog/looks/main/assets/images/
```

**Starter files**

The command also attempts to copy sample stub files into the plugin. These include starter files for things like:

- `plugin.php`
- `config.json`
- `look.json`
- starter CSS
- starter JavaScript
- starter home view
- starter controller

**Placeholder replacement**

When stub files are copied, the CLI replaces placeholders such as:

- `{NAMESPACE}`

This allows generated code to match the plugin's namespace automatically.

For example, a plugin id like `basic-user` becomes a namespace like:

```php
BasicUser
```

**Force mode**

If the target plugin already exists:

- without `--force`, the CLI stops and warns you
- with `--force`, it continues and overwrites scaffold files

**Practical recipe**

```php
// Create a new plugin called school-fees
php thunder make:plugin school-fees

// Then add a model
php thunder make:model school-fees invoice

// Then add a migration
php thunder make:migration school-fees create-invoices-table
```

**Important note**

`make:plugin` creates the folder structure and starter files, but it does not register routes, run migrations, or seed data automatically. Those steps still depend on your plugin code and migration execution.
