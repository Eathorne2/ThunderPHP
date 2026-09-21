---
title: "help Command"
slug: "help-command"
description: "Reference for the help command and the built-in command list displayed by the Thunder CLI."
published: true
order: 2
source_id: 132
keywords: ["help", "command", "reference", "built-in", "list", "displayed", "thunder", "cli", "line", "tool", "usage", "commands", "purpose", "displays", "output", "styled", "practical", "note"]
---

**Purpose**

The `help` command prints the available Thunder CLI commands grouped by purpose. It is the main discovery tool for anyone using the CLI for the first time.

**Usage**

```php
php thunder help
```

**What it displays**

The help screen includes:

- generator commands
- migration commands
- informational commands

The command list currently includes:

```php
make:plugin <name> [--force]
make:controller <plugin> <name>
make:model <plugin> <name>
make:migration <plugin> <name>
make:pager <plugin> <name>
make:view <plugin> <name>
help
migrate <plugin|all> [filename]
migrate:rollback <plugin|all>
migrate:refresh <plugin> [filename]
migrate:status [plugin|all]
list:plugins
```

**How the output is styled**

The CLI uses ANSI escape codes internally to color text output. That means:

- headings are highlighted
- success and failure states are easier to distinguish
- command names are visually separated from descriptions

This is a usability feature only. It does not affect how commands behave.

**When to use it**

Use `help` when:

- learning the CLI
- confirming argument order
- checking whether a command exists
- reviewing migration command syntax before running database changes

**Practical note**

Because the help output is manually defined, it reflects the current CLI implementation exactly. If you add custom Thunder commands later, update the help screen too so the documentation and the CLI stay aligned.
