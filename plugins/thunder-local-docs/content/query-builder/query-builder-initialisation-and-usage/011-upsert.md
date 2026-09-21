---
title: "UPSERT"
slug: "upsert"
description: "Insert or update on conflict."
published: true
order: 11
source_id: 23
keywords: ["upsert", "insert", "update", "conflict", "query", "builder", "initialisation", "usage", "notes"]
---

```php
$userModel->upsert(
    [
        ['email' => 'john@example.com', 'name' => 'John']
    ],
    ['email'],
    ['name']
);
```
**Notes**

`MySQL → ON DUPLICATE KEY`

`PostgreSQL → ON CONFLICT`

`Fallback → manual transaction`
