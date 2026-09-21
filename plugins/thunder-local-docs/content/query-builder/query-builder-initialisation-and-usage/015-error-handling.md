---
title: "Error Handling"
slug: "error-handling"
description: "How to deal with query errors"
published: true
order: 15
source_id: 29
keywords: ["error", "handling", "deal", "query", "errors", "builder", "initialisation", "usage", "available", "methods"]
---

```php
try {
    $userModel->insert([...]);
} catch (Exception $e) {
    echo $userModel->getLastError();
}
```

**Available Methods**

```php
$userModel->getLastError();
$userModel->getLastErrorCode();
$userModel->getLastSqlState();
$userModel->getAffectedRows();
$userModel->getLastInsertId();
```
