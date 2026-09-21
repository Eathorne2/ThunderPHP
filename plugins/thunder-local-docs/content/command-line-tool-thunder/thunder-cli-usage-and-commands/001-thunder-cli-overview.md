---
title: "Thunder CLI Overview"
slug: "thunder-cli-overview"
description: "Introduction to the Thunder CLI tool, its purpose, and the main command groups available in version 1.0.0."
published: true
order: 1
source_id: 131
keywords: ["thunder", "cli", "overview", "introduction", "tool", "purpose", "main", "command", "groups", "available", "version", "line", "usage", "commands", "dispatched", "typical", "flow", "assumes"]
---

**Overview**

The `thunder` CLI tool is the command-line companion for ThunderPHP. It is responsible for scaffolding plugin files, generating boilerplate classes, validating plugin structure, and running database migrations.

In the provided implementation, the CLI is represented by the `Thunder\Thunder` class and reports itself as version `1.0.0`. It groups its features into three broad areas:

- generators such as `make:plugin`, `make:model`, and `make:migration`
- migration commands such as `migrate`, `migrate:rollback`, and `migrate:status`
- information and maintenance commands such as `help`, `list:plugins`, and `validate`

**Main command groups**

The CLI help output shows these major groups:

- **Generators**
- **Migration**
- **Info**

That means the tool is designed to support both **project scaffolding** and **database lifecycle management** from one place.

**How commands are dispatched**

The Thunder CLI class uses separate methods to handle different command families:

- `help()` displays command usage
- `make()` handles file and plugin generation
- `list()` prints installed plugin information
- `validate()` checks plugin structure and required metadata
- `migrate()` dispatches migration-related actions

This structure makes the CLI easy to extend later because each family of commands has its own entry point.

**Typical usage flow**

A common real-world flow looks like this:

```php
// 1. Create a plugin
php thunder make:plugin blog

// 2. Create a model
php thunder make:model blog post

// 3. Create a migration
php thunder make:migration blog create-posts-table

// 4. Run migrations
php thunder migrate blog

// 5. Check status
php thunder migrate:status blog
```

**What the CLI assumes**

The implementation assumes a plugin-based directory structure rooted around the `plugins/` folder. Most generated files are placed inside plugin folders such as:

```php
plugins/blog/controllers/
plugins/blog/models/
plugins/blog/migrations/
plugins/blog/looks/
```

Because of that, the CLI is tightly integrated with the ThunderPHP plugin architecture rather than being a generic framework-agnostic generator.
