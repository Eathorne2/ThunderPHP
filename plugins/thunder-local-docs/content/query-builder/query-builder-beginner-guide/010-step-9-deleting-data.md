---
title: "Step 9: Deleting Data"
slug: "step-9-deleting-data"
description: "Delete rows safely by targeting them with a where clause."
published: true
order: 10
source_id: 42
keywords: ["step", "deleting", "data", "delete", "rows", "safely", "targeting", "them", "clause", "query", "builder", "beginner", "guide"]
---

Use `delete()` to remove rows.

```php
$userModel
    ->where('id', 1)
    ->delete();
```
