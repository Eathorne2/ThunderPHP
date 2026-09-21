---
title: "Pagination"
slug: "pagination"
description: "Automatically handles limit, offset, and total count."
published: true
order: 6
source_id: 17
keywords: ["pagination", "automatically", "handles", "limit", "offset", "total", "count", "query", "builder", "initialisation", "usage", "structure"]
---

```php
$results = $userModel->paginate(15, 1);
```

**Structure**

```php
[
  'data' => [...],
  'current_page' => 1,
  'per_page' => 15,
  'total' => 100,
  'last_page' => 7,
  'from' => 1,
  'to' => 15
]
```
