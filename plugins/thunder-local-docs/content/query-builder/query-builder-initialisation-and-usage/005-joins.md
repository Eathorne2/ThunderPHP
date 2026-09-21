---
title: "Joins"
slug: "joins"
description: "Combine multiple tables"
published: true
order: 5
source_id: 15
keywords: ["joins", "combine", "multiple", "tables", "query", "builder", "initialisation", "usage"]
---

```php
$postModel->join('users', 'posts.user_id', '=', 'users.id');

$postModel->leftJoin('comments', 'posts.id', '=', 'comments.post_id');

$postModel->rightJoin('authors', 'posts.author_id', '=', 'authors.id');
```
