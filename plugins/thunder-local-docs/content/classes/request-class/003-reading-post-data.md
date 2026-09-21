---
title: "Reading POST Data"
slug: "reading-post-data"
description: "How to retrieve posted form values using `post()` and `input()`."
published: true
order: 3
source_id: 69
keywords: ["reading", "post", "data", "retrieve", "posted", "form", "values", "input", "classes", "request", "class"]
---

Use `post()` when you want to read submitted form values from `$_POST`.

If you call it with no key, it returns the whole `$_POST` array.

```php
$req = new \\Core\Request();

$data = $req->post();
print_r($data);
```

If you pass a key, it returns that value if it exists.

```php
$req = new \\Core\Request();

$username = $req->post('username');
```

You can also pass a default value:

```php
$req = new \\Core\Request();

$country = $req->post('country', 'Zambia');
```

The `input()` method is simply a convenient alias for reading posted values with a default fallback.

```php
$req = new \\Core\Request();

$email = $req->input('email');
$role = $req->input('role', 'user');
```

One important improvement in this class is that it uses key existence checks instead of `empty()`. That means values like `0` and `'0'` are not incorrectly treated as missing.

Example:

```php
$req = new \\Core\Request();

$status = $req->post('status', 1);

// if the submitted value was '0', you still get '0'
var_dump($status);
```

This makes the class much safer for forms where zero is a valid value.
