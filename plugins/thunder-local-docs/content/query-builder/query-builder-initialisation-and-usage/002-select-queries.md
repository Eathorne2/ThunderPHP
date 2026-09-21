---
title: "SELECT Queries"
slug: "select-queries"
description: "Retrieve data from a table."
published: true
order: 2
source_id: 13
keywords: ["select", "queries", "retrieve", "data", "table", "query", "builder", "initialisation", "usage", "all", "columns", "specific", "alias", "raw"]
---

**All columns**

```php
$users = $userModel->get();
```

**Specific columns**

```php
$users = $userModel->select('id', 'name')->get();
```

**With alias**
```php
$users = $userModel->select('name as username')->get();
```

**Raw select**
```php
$users = $userModel
    ->selectRaw('COUNT(*) as total')
    ->get();
```
