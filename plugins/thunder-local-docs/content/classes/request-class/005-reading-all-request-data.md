---
title: "Reading All Request Data"
slug: "reading-all-request-data"
description: "How to use `all()` and when to prefer `post()` or `get()` instead."
published: true
order: 5
source_id: 71
keywords: ["reading", "all", "request", "data", "prefer", "post", "get", "instead", "classes", "class"]
---

The `all()` method reads from `$_REQUEST`, which usually combines `$_GET`, `$_POST`, and in some environments cookies as well.

With no key, it returns the full request data.

```php
$req = new \\Core\Request();

$data = $req->all();
print_r($data);
```

With a key, it returns one request value:

```php
$req = new \\Core\Request();

$id = $req->all('id');
```

You can also provide a default:

```php
$req = new \\Core\Request();

$tab = $req->all('tab', 'general');
```

Even though `all()` is available, it is usually better to prefer `post()` and `get()` when you know exactly where the value should come from. That keeps your code more explicit and easier to debug.

Recommended:

```php
$req = new \\Core\Request();

$name = $req->post('name');
$page = $req->get('page', 1);
```

Less explicit:

```php
$req = new \\Core\Request();

$name = $req->all('name');
$page = $req->all('page', 1);
```

Use `all()` only when you intentionally want to accept the value from either source.
