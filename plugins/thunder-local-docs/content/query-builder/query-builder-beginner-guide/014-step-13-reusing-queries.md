---
title: "Step 13: Reusing Queries"
slug: "step-13-reusing-queries"
description: "Reuse a base query with cloning to avoid repetition."
published: true
order: 14
source_id: 46
keywords: ["step", "reusing", "queries", "reuse", "base", "query", "cloning", "avoid", "repetition", "builder", "beginner", "guide"]
---

A useful pattern is to build a base query once and then clone it for different variations.

```php
$base = $userModel->where('status', 'active');

$admins = (clone $base)->where('role', 'admin')->get();
$users  = (clone $base)->where('role', 'user')->get();
```
