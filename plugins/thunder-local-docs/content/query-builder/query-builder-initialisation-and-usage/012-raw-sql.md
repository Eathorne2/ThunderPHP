---
title: "RAW SQL"
slug: "raw-sql"
description: "Execute raw queries directly."
published: true
order: 12
source_id: 24
keywords: ["raw", "sql", "execute", "queries", "directly", "query", "builder", "initialisation", "usage", "single", "row"]
---

```php
$userModel->raw(
    'SELECT * FROM users WHERE status = ?',
    ['active']
);
```

**Single Row**

```php
$user = $userModel->raw(
    'SELECT * FROM users WHERE id = ?',
    [1],
    false
);
```
*Param Structure*
```php
[
     $sql,
     $bindings = [],
     $fetchAll = true,
]
