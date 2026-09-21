---
title: "Dot Notation"
slug: "dot-notation"
description: "Working with nested session data using dot notation."
published: true
order: 4
source_id: 55
keywords: ["dot", "notation", "working", "nested", "session", "data", "classes", "class"]
---

The session class supports nested data using dot notation.

```php
$session->set('cart.items', []);

$session->push('cart.items', [
    'id' => 1,
    'qty' => 2
]);

$total = $session->get('cart.summary.total', 0);
```
