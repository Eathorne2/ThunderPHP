---
title: "Basic Example"
slug: "basic-example"
description: "Read users and echo their names through a loop"
published: true
order: 2
source_id: 12
keywords: ["basic", "example", "read", "users", "echo", "names", "through", "loop", "query", "builder", "initialisation", "usage"]
---

```php
$userModel = new User;

$users = $userModel
    ->where('status', 'active')
    ->orderBy('name')
    ->get();

foreach ($users as $user) {
    echo $user->name;
}
```
