---
title: "Practical Recipes"
slug: "practical-recipes"
description: "Some useful ways to use the query builder"
published: true
order: 16
source_id: 30
keywords: ["practical", "recipes", "some", "useful", "ways", "query", "builder", "initialisation", "usage", "get", "active", "users", "latest", "posts", "count", "records", "paginated", "list", "insert", "update"]
---

**Get Active Users**

```php
$users = $userModel
    ->where('status', 'active')
    ->get();
```

**Get Latest Posts**

```php
$posts = $postModel
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();
```

**Count Records**

```php
$total = $userModel->count();
```

**Paginated List**

```php
$page = $_GET['page'] ?? 1;

$results = $userModel->paginate(10, $page);

foreach ($results['data'] as $user) {
    echo $user->name;
}
```

**Insert Then Update**

```php
$userModel->insert([
    'name' => 'Jane'
]);

$id = $userModel->getLastInsertId();

$userModel
    ->where('id', $id)
    ->update(['status' => 'active']);
```
