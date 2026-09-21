---
title: "Step 14: Debugging Queries"
slug: "step-14-debugging-queries"
description: "Inspect generated SQL and bindings for debugging."
published: true
order: 15
source_id: 47
keywords: ["step", "debugging", "queries", "inspect", "generated", "sql", "bindings", "query", "builder", "beginner", "guide"]
---

Use `toSql()` to see the generated SQL and `getBindings()` to inspect the bound values.

View SQL:

```php
$sql = $userModel
    ->where('status', 'active')
    ->toSql();

echo $sql;
```

View bindings:

```php
print_r($userModel->getBindings());
```
