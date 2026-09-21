---
title: "Setting and Getting Data"
slug: "setting-and-getting-data"
description: "How to store and retrieve session values."
published: true
order: 3
source_id: 54
keywords: ["setting", "getting", "data", "store", "retrieve", "session", "values", "classes", "class", "check", "key", "exists", "remove", "pop", "value"]
---

**Store session data**

```php
$session->set('username', 'john');

$session->set([
    'email' => 'john@example.com',
    'role' => 'admin'
]);
```

**Retrieve data**

```php
$username = $session->get('username', 'guest');
```

**Check if a key exists**

```php
if($session->has('email')){
    echo $session->get('email');
}
```

**Remove data**

```php
$session->remove('username');
```

**Pop a value**

```php
$message = $session->pop('notice');
```
