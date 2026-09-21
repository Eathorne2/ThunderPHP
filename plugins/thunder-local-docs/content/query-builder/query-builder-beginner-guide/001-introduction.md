---
title: "Introduction"
slug: "introduction"
description: "Learn what the Query Builder is and why it is useful."
published: true
order: 1
source_id: 33
keywords: ["introduction", "learn", "query", "builder", "why", "useful", "beginner", "guide"]
---

The Query Builder in ThunderPHP allows you to interact with your database using simple, readable PHP code instead of writing raw SQL.

Instead of writing:

```php
SELECT * FROM users WHERE status = 'active'
```

You write:

```php
$userModel->where('status', 'active')->get();
```

It is safer, easier to read, and helps prevent SQL injection automatically.
