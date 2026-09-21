---
title: "Step 5: Limiting Results"
slug: "step-5-limiting-results"
description: "Limit how many rows are returned."
published: true
order: 6
source_id: 38
keywords: ["step", "limiting", "results", "limit", "many", "rows", "returned", "query", "builder", "beginner", "guide"]
---

Use `limit()` when you only want a small number of rows.

```php
$users = $userModel
    ->limit(10)
    ->get();
```
