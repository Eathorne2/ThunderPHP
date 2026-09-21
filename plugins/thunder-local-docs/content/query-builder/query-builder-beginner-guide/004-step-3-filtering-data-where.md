---
title: "Step 3: Filtering Data (WHERE)"
slug: "step-3-filtering-data-where"
description: "Use where clauses to filter the records you need."
published: true
order: 4
source_id: 36
keywords: ["step", "filtering", "data", "clauses", "filter", "records", "need", "query", "builder", "beginner", "guide"]
---

The `where()` method lets you filter your results.

Basic filtering:

```php
$users = $userModel
    ->where('status', 'active')
    ->get();
```

Using operators:

```php
$users = $userModel
    ->where('age', '>', 18)
    ->get();
```

Multiple conditions:

```php
$users = $userModel
    ->where('status', 'active')
    ->where('age', '>', 18)
    ->get();
```

OR conditions:

```php
$users = $userModel
    ->where('role', 'admin')
    ->orWhere('role', 'moderator')
    ->get();
```
