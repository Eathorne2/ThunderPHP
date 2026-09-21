---
title: "Insert"
slug: "insert"
description: "How to insert data and get the id of the inserted row. The id is based on the primary key of the table"
published: true
order: 8
source_id: 19
keywords: ["insert", "data", "get", "id", "inserted", "row", "based", "primary", "key", "table", "query", "builder", "initialisation", "usage", "last"]
---

```php
$userModel->insert([
    'name' => 'John',
    'email' => 'john@example.com'
]);
```
**Last Insert ID**

```php
$id = $userModel->getLastInsertId();
```
