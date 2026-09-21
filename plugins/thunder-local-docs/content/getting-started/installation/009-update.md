---
title: "UPDATE"
slug: "update"
description: "Updating records"
published: true
order: 9
source_id: 20
keywords: ["update", "updating", "records", "getting", "started", "installation"]
---

```php
$rows = $userModel
    ->where('id', 5)
    ->update([
        'name' => 'Updated Name'
    ]);
```
