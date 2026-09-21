---
title: "Understanding the Main CLI Commands"
slug: "understanding-the-main-cli-commands"
description: "A simple overview of the most important Thunder commands a beginner will use first."
published: true
order: 2
source_id: 150
keywords: ["understanding", "main", "cli", "commands", "simple", "overview", "most", "important", "thunder", "beginner", "first", "command", "line", "tool", "getting", "started", "often", "each", "one", "does", "start", "help", "good", "habit"]
---

**The commands you will use most often**

When starting out, you do not need every command immediately. The most useful beginner commands are:

```php
php thunder help
php thunder make:plugin <name>
php thunder make:model <plugin> <name>
php thunder make:migration <plugin> <name>
php thunder migrate <plugin|all>
php thunder migrate:status [plugin|all]
php thunder migrate:rollback <plugin|all>
php thunder migrate:refresh <plugin> [filename]
php thunder list:plugins
```

**What each one does**

- `help` shows available commands
- `make:plugin` creates a plugin skeleton
- `make:model` creates a model stub inside a plugin
- `make:migration` creates a migration file inside a plugin
- `migrate` runs pending migrations
- `migrate:status` shows which migrations have run and which are still pending
- `migrate:rollback` reverses the latest migration batch
- `migrate:refresh` performs a rollback and then re-runs the migration
- `list:plugins` shows installed plugins

**Start with help**

If you forget command syntax, use:

```php
php thunder help
```

This prints the built-in help screen and is the easiest way to confirm argument order before doing anything destructive.

**A good habit**

A safe beginner habit is:

```php
php thunder migrate:status blog
```

before and after every migration run. That makes it easy to see what changed.
