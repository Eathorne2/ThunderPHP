---
title: "Subqueries"
slug: "subqueries"
description: "Using sub queries or nested queries"
published: true
order: 13
source_id: 25
keywords: ["subqueries", "sub", "queries", "nested", "getting", "started", "installation"]
---

```php
$postModel->where('user_id', function($q) {
    $q->from('users')
      ->select('id')
      ->where('status', 'active');
});
```
