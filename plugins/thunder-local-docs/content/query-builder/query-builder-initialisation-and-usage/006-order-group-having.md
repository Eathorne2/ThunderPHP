---
title: "ORDER, GROUP & HAVING"
slug: "order-group-having"
description: "How to order and group results"
published: true
order: 6
source_id: 16
keywords: ["order", "group", "having", "results", "query", "builder", "initialisation", "usage", "ordering", "grouping", "limit", "offset"]
---

**Ordering**

```php
$userModel->orderBy('name', 'ASC');
$userModel->orderBy('created_at', 'DESC');
```

**Grouping**

```php
$userModel->groupBy('role');
```

**Having**

```php
$userModel->having('total', '>', 5);
```

**Limit & Offset**

```php
$userModel->limit(10);
$userModel->limit(10)->offset(20);
```
