---
title: "Step 11: Using Raw SQL"
slug: "step-11-using-raw-sql"
description: "Run raw SQL when the builder syntax is not enough."
published: true
order: 12
source_id: 44
keywords: ["step", "raw", "sql", "run", "builder", "syntax", "enough", "query", "beginner", "guide"]
---

Sometimes you need full control over the SQL being executed. In that case, use `raw()`.

```php
$users = $userModel->raw(
    'SELECT * FROM users WHERE status = ?',
    ['active']
);
```

Use raw SQL only when the Query Builder cannot handle your query cleanly.
