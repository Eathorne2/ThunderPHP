---
title: "Checking Migration Status"
slug: "checking-migration-status"
description: "How to read migration status output and understand the difference between pending and ran migrations."
published: true
order: 7
source_id: 155
keywords: ["checking", "migration", "status", "read", "output", "understand", "difference", "between", "pending", "ran", "migrations", "command", "line", "tool", "thunder", "getting", "started", "cli", "step", "check", "see", "why", "matters", "common"]
---

**Step 5: check status**

Use the status command to see whether migrations are pending or already executed.

```php
php thunder migrate:status blog
```

Or for everything:

```php
php thunder migrate:status all
```

**What you will see**

Thunder prints each migration file and labels it as one of these:

- `[RAN]`
- `[PENDING]`

For ran migrations, it also shows useful metadata such as:

- batch number
- run timestamp

**Why this matters**

This gives you a quick answer to questions like:

- did my migration actually run?
- which file is still pending?
- what batch was this migration part of?

**A common workflow**

```php
php thunder migrate:status blog
php thunder migrate blog
php thunder migrate:status blog
```

This before-and-after pattern is a very good habit while learning.
