---
title: "Best Practices and Caveats"
slug: "best-practices-and-caveats"
description: "Recommended usage patterns, naming conventions, safety guidance, and caveats specific to the current Thunder migration implementation."
published: true
order: 18
source_id: 148
keywords: ["best", "practices", "caveats", "recommended", "usage", "patterns", "naming", "conventions", "safety", "guidance", "specific", "current", "thunder", "migration", "implementation", "database", "migrations", "overview", "clear", "names", "prefer", "new", "over", "editing"]
---

**Use clear migration names**

Choose file names that describe the schema action clearly:

```php
create-users-table
add-status-to-orders
create-school-fees-table
add-foreign-key-to-results
```

This makes status output and migration history far easier to read.

**Prefer new migrations over editing old ones**

Once a migration has run in a shared environment, avoid editing it. Create a new migration for the next change instead.

That keeps migration history stable and avoids confusion around tracker checksums.

**Keep `up()` and `down()` symmetrical**

A good rollback depends on `down()` truly reversing `up()`.

Examples:

- if `up()` adds an index, `down()` should drop it
- if `up()` adds a column, `down()` should remove it
- if `up()` adds a foreign key, `down()` should drop that foreign key

**Be careful with destructive changes**

Dropping columns or tables can destroy data permanently. Even though Thunder makes this easy, production usage should still follow safe database practices such as:

- backups before schema changes
- testing migrations on staging first
- reviewing rollback consequences carefully

**Remember that addColumn uses raw SQL fragments**

Methods like `addColumn()` are flexible because they accept raw SQL, but they also rely on you to write correct SQL syntax. Thunder does not abstract every SQL detail for you.

**Understand batch-based rollback**

`migrate:rollback` does not mean "undo everything." It means "undo the latest tracked batch." Plan your migration runs with that in mind.

**Tracker table is part of the system**

Do not casually delete rows from `thunder_migrations` unless you understand the consequences. That table controls:

- pending detection
- rollback scope
- migration history

**Use explicit names for important indexes and constraints**

Thunder can generate some names automatically for altered-table indexes and foreign keys, but explicit names are easier to maintain, debug, and drop later.

**Development vs production mindset**

During development, `migrate:refresh` is convenient.

In production, prefer disciplined forward-only migrations unless rollback is genuinely required and thoroughly tested.
