---
title: "Step 10: Joining Tables"
slug: "step-10-joining-tables"
description: "Join related tables to fetch combined data."
published: true
order: 11
source_id: 43
keywords: ["step", "joining", "tables", "join", "related", "fetch", "combined", "data", "query", "builder", "beginner", "guide"]
---

Use joins when you need data from more than one table.

```php
$posts = $postModel
    ->join('users', 'posts.user_id', '=', 'users.id')
    ->get();
```
