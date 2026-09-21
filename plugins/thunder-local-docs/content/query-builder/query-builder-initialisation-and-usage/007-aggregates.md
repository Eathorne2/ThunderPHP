---
title: "Aggregates"
slug: "aggregates"
description: "Using aggregates like SUM, AVG, MIN etc"
published: true
order: 7
source_id: 18
keywords: ["aggregates", "like", "sum", "avg", "min", "etc", "query", "builder", "initialisation", "usage"]
---

```php
$count = $userModel->count();

$total = $userModel
    ->selectRaw('SUM(amount) as total')
    ->first();

echo $total->total;

```
