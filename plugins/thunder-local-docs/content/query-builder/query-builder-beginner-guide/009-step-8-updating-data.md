---
title: "Step 8: Updating Data"
slug: "step-8-updating-data"
description: "Update existing rows using a where clause."
published: true
order: 9
source_id: 41
keywords: ["step", "updating", "data", "update", "existing", "rows", "clause", "query", "builder", "beginner", "guide"]
---

Use `update()` to change existing rows.

```php
$userModel
    ->where('id', 1)
    ->update([
        'name' => 'Updated Name'
    ]);
```
