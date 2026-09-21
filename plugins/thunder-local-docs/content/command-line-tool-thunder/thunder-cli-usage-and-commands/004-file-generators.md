---
title: "File Generators"
slug: "file-generators"
description: "Reference for generator commands that create controllers, models, views, migrations, pagers, and validators."
published: true
order: 4
source_id: 134
keywords: ["file", "generators", "reference", "generator", "commands", "create", "controllers", "models", "views", "migrations", "pagers", "validators", "command", "line", "tool", "thunder", "cli", "usage", "overview", "available", "general", "behavior", "controller", "generation"]
---

**Overview**

Thunder provides a family of `make:*` commands for generating files inside a plugin. These commands use stub templates and write the generated files into the correct plugin folders.

**Available generator commands**

```php
php thunder make:controller <plugin> <name>
php thunder make:model <plugin> <name>
php thunder make:migration <plugin> <name>
php thunder make:pager <plugin> <name>
php thunder make:validator <plugin> <name>
php thunder make:view <plugin> <name>
```

**General behavior**

All generator commands:

- require a plugin name
- require a file or class name
- create missing folders if necessary
- use sample stub files when available

**Controller generation**

```php
php thunder make:controller blog admin/posts
```

This allows nested paths. If the name contains folders, Thunder creates the subfolder first, then writes the controller file there.

**Model generation**

```php
php thunder make:model blog post
```

Model files are automatically uppercased on the first letter:

```php
plugins/blog/models/Post.php
```

**Migration generation**

```php
php thunder make:migration blog create-posts-table
```

Migration filenames are timestamp-prefixed automatically:

```php
plugins/blog/migrations/2026-04-21_153000_create-posts-table.php
```

This is important because migration execution order is based on filename sorting.

**View generation**

```php
php thunder make:view blog frontend/post-list
```

Views can also be placed inside nested folders when the name includes a path.

**How class names are built**

The CLI sanitizes the provided name before generating classes. It:

- removes unsupported characters
- converts hyphens to underscores
- uppercases the first letter

So a name like:

```php
user-profile
```

becomes a class name like:

```php
User_profile
```

**Practical recipe**

```php
// Generate a full starting set for a plugin
php thunder make:controller shop products
php thunder make:model shop product
php thunder make:migration shop create-products-table
php thunder make:view shop admin/products
```

**Important limitation**

These commands generate files only. They do not automatically wire the files into plugin hooks or routes. After generation, you still need to connect the code to your plugin's runtime behavior.
