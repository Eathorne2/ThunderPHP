---
title: "Step 2: Getting Data (SELECT)"
slug: "step-2-getting-data-select"
description: "Fetch all rows, specific columns, or a single record."
published: true
order: 3
source_id: 35
keywords: ["step", "getting", "data", "select", "fetch", "all", "rows", "specific", "columns", "single", "record", "query", "builder", "beginner", "guide"]
---

Use `get()` when you want multiple rows and `first()` when you only need one row.

Get all rows:

```php
$users = $userModel->get();

foreach ($users as $user) {
    echo $user->name;
}
```

Get specific columns:

```php
$users = $userModel
    ->select('id', 'name')
    ->get();
```

Get one row:

```php
$user = $userModel
    ->where('id', 1)
    ->first();

echo $user->name;
```
