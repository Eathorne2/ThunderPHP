---
title: "WHERE Clauses"
slug: "where-clauses"
description: "How to Filter query results with the `WHERE` clause. Provided below are some examples:"
published: true
order: 4
source_id: 14
keywords: ["clauses", "filter", "query", "results", "clause", "provided", "below", "some", "examples", "builder", "initialisation", "usage", "basic", "operator", "condition", "null", "between", "nested", "conditions", "raw"]
---

**Basic**

```php
$userModel->where('status', 'active');
```

**With operator**

```php
$userModel->where('age', '>', 18);
```

**OR condition**

```php
$userModel->where('role', 'admin')
          ->orWhere('role', 'moderator');
```

**WHERE IN**

```php
$userModel->whereIn('id', [1,2,3]);
```

**WHERE NULL**

```php
$userModel->whereNull('deleted_at');
```

**BETWEEN**

```php
$userModel->whereBetween('age', [18, 30]);
```

**Nested Conditions**

```php
$userModel->whereNested(function($q) {
    $q->where('role', 'admin')
      ->orWhere('role', 'superuser');
});
```

**Raw WHERE**

```php
$userModel->whereRaw('age > ?', [18]);
```
