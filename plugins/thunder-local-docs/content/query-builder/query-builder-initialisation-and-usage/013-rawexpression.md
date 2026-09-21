---
title: "RawExpression"
slug: "rawexpression"
description: "Used internally for raw SQL fragments (e.g., `selectRaw`, `orderByRaw`)."
published: true
order: 13
source_id: 26
keywords: ["rawexpression", "internally", "raw", "sql", "fragments", "selectraw", "orderbyraw", "query", "builder", "initialisation", "usage"]
---

```php
$userModel->selectRaw('COUNT(*) as total');
```

**When to Use***

- Complex SQL expressions
- Database-specific functions
- Performance-critical queries
