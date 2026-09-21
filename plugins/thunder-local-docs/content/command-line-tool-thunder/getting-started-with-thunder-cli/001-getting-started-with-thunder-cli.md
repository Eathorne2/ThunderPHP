---
title: "Getting Started with Thunder CLI"
slug: "getting-started-with-thunder-cli"
description: "A beginner-friendly introduction to the Thunder CLI tool, what it does, and where it fits in a ThunderPHP project."
published: true
order: 1
source_id: 149
keywords: ["getting", "started", "thunder", "cli", "beginner-friendly", "introduction", "tool", "does", "fits", "thunderphp", "project", "command", "line", "welcome", "guide", "covers", "basic", "idea", "things", "go"]
---

**Welcome**

The `thunder` CLI tool is the command-line utility used to scaffold plugin files and manage database migrations in ThunderPHP.

If you are new to ThunderPHP, think of `thunder` as the tool that helps you do two big jobs:

- generate files and folders quickly
- create and run database migrations safely

In the provided implementation, the CLI exposes generator commands such as `make:plugin`, `make:model`, and `make:migration`, along with migration commands such as `migrate`, `migrate:rollback`, `migrate:refresh`, and `migrate:status`.

**What this guide covers**

This beginner guide focuses on the most practical workflow:

1. create a plugin
2. generate a migration
3. write the migration
4. run the migration
5. check status
6. roll back if needed

**The basic idea**

A common beginner flow looks like this:

```php
// Create a plugin
php thunder make:plugin blog

// Create a migration inside that plugin
php thunder make:migration blog create-posts-table

// Run pending migrations for the plugin
php thunder migrate blog
```

That is the foundation of the migration system.

**Where things go**

The CLI writes generated files into plugin folders under `plugins/`. For example:

```php
plugins/blog/
plugins/blog/models/
plugins/blog/controllers/
plugins/blog/migrations/
plugins/blog/looks/
```

So before learning migrations deeply, it helps to understand that ThunderPHP is plugin-first. The CLI is designed around that structure.
