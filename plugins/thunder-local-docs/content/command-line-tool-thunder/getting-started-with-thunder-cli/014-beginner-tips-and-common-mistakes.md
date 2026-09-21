---
title: "Beginner Tips and Common Mistakes"
slug: "beginner-tips-and-common-mistakes"
description: "Practical advice to help beginners avoid the most common migration mistakes."
published: true
order: 14
source_id: 162
keywords: ["beginner", "tips", "common", "mistakes", "practical", "advice", "help", "beginners", "avoid", "most", "migration", "command", "line", "tool", "thunder", "getting", "started", "cli", "save", "time", "keep", "up", "down", "symmetrical"]
---

**Tips that save time**

Here are a few beginner-friendly rules that make Thunder migrations easier to work with.

**1. Keep `up()` and `down()` symmetrical**

If `up()` creates a table, `down()` should usually drop it.

If `up()` adds a column, `down()` should usually remove it.

**2. Check status often**

Use:

```php
php thunder migrate:status blog
```

before and after migration commands.

**3. Do not rename files after running them**

Thunder tracks migrations by filename. Renaming a file after it has run can confuse the migration history.

**4. Be careful with refresh on real data**

`migrate:refresh` is powerful during development, but dangerous if the rollback deletes live tables.

**5. Keep one clear purpose per migration**

Good migration names are specific:

```php
create-posts-table
add-excerpt-to-posts
add-user-id-index-to-posts
```

That makes status output and troubleshooting much easier.

**6. Start small**

For your first few migrations, focus on:

- create table
- add key
- insert seed data
- rollback cleanly

That is enough to build confidence before moving on to more advanced schema changes.
