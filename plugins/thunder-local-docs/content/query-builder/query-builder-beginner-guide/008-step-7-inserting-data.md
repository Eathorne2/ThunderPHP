---
title: "Step 7: Inserting Data"
slug: "step-7-inserting-data"
description: "Insert a new row and read back the insert id."
published: true
order: 8
source_id: 40
keywords: ["step", "inserting", "data", "insert", "new", "row", "read", "back", "id", "query", "builder", "beginner", "guide"]
---

Use `insert()` to add a new record.

```php
$userModel->insert([
    'name' => 'John',
    'email' => 'john@example.com'
]);
```

Get the inserted id:

```php
$id = $userModel->getLastInsertId();
```
