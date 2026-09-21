---
title: "UNION"
slug: "union"
description: "combining tables with union"
published: true
order: 10
source_id: 22
keywords: ["union", "combining", "tables", "query", "builder", "initialisation", "usage"]
---

```php
$admins = $userModel->where('role', 'admin');
$mods   = $userModel->where('role', 'moderator');

$results = $admins->union($mods)->get();
```
