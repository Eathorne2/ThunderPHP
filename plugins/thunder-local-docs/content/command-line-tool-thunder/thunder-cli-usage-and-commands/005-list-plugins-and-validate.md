---
title: "list:plugins and validate"
slug: "list-plugins-and-validate"
description: "How to inspect installed plugins and validate plugin metadata using Thunder CLI commands."
published: true
order: 5
source_id: 135
keywords: ["list", "plugins", "validate", "inspect", "installed", "plugin", "metadata", "thunder", "cli", "commands", "command", "line", "tool", "usage", "reads", "useful", "checks", "report", "looks", "like", "practical", "recipe", "important", "note"]
---

**list:plugins**

The `list:plugins` command scans the `plugins/` folder and displays metadata for installed plugins.

**Usage**

```php
php thunder list:plugins
```

**What it reads**

For each plugin folder, the command looks for:

```php
plugins/{plugin}/config.json
```

If the file exists and contains valid JSON, the CLI prints values such as:

- plugin name
- plugin id
- author
- version
- active status

**When this is useful**

Use `list:plugins` when:

- checking whether a plugin exists
- reviewing plugin metadata quickly
- confirming active or inactive status
- debugging plugin installation issues

---

**validate**

The `validate` command checks plugin structure at a basic metadata level.

**Usage**

```php
php thunder validate
```

**What it checks**

A plugin is considered valid only if:

- `config.json` exists
- `config.json` contains valid JSON
- required fields are present

The required fields are:

```php
name
id
routes
version
```

**What the report looks like**

The CLI prints one result per plugin:

```php
✅ plugin-name — valid
❌ plugin-name — missing config.json
❌ plugin-name — malformed JSON
❌ plugin-name — missing fields: routes, version
```

It then prints a summary count of valid and invalid plugins.

**Practical recipe**

Run this after manually creating or copying plugins:

```php
php thunder validate
php thunder list:plugins
```

This gives you both a structural report and a readable metadata summary.

**Important note**

`validate` checks the plugin manifest only. It does not validate controller code, migrations, database schema, or hook registration.
