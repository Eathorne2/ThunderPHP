---
title: "DELETE"
slug: "delete"
description: "How to delete records"
published: true
order: 10
source_id: 21
keywords: ["delete", "records", "query", "builder", "initialisation", "usage"]
---

```php
$rows = $userModel
    ->where('id', 10)
    ->delete();
```
