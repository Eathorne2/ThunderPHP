---
title: "Step 4: Sorting Results"
slug: "step-4-sorting-results"
description: "Sort rows in ascending or descending order."
published: true
order: 5
source_id: 37
keywords: ["step", "sorting", "results", "sort", "rows", "ascending", "descending", "order", "query", "builder", "beginner", "guide"]
---

Use `orderBy()` to control the order of the returned rows.

```php
$users = $userModel
    ->orderBy('name', 'ASC')
    ->get();
```
