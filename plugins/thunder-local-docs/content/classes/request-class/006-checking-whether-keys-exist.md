---
title: "Checking Whether Keys Exist"
slug: "checking-whether-keys-exist"
description: "Using `has_post()`, `has_get()`, and `has_file()` to check for request input safely."
published: true
order: 6
source_id: 72
keywords: ["checking", "whether", "keys", "exist", "has_post", "has_get", "has_file", "check", "request", "input", "safely", "classes", "class"]
---

Sometimes you do not want the actual value yet. You only want to know whether a key exists. The `Request` class provides helper methods for that.

Use `has_post()` to check whether a POST key exists.

```php
$req = new \\Core\Request();

if($req->has_post('email'))
{
	echo "Email field was submitted";
}
```

Use `has_get()` to check whether a GET key exists.

```php
$req = new \\Core\Request();

if($req->has_get('page'))
{
	echo "Page parameter exists";
}
```

Use `has_file()` to check whether a file input contains an uploaded file.

```php
$req = new \\Core\Request();

if($req->has_file('avatar'))
{
	echo "Avatar file was selected";
}
```

This is especially useful for file inputs because not every request containing `$_FILES['avatar']` means that a file was actually chosen.

It also works for array-style file inputs such as `photos[]`.

```php
$req = new \\Core\Request();

if($req->has_file('photos'))
{
	echo "At least one photo exists";
}
```

These helpers make conditions cleaner and reduce low-level checks against PHP superglobals.
