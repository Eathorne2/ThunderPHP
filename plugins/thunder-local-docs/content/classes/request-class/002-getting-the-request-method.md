---
title: "Getting the Request Method"
slug: "getting-the-request-method"
description: "Methods for checking the current HTTP method and common request state helpers."
published: true
order: 2
source_id: 68
keywords: ["getting", "request", "method", "methods", "checking", "current", "http", "common", "state", "helpers", "classes", "class"]
---

Use the request method helpers when you want to know how the current page was accessed.

The main method is `method()`, which returns the current request method as an uppercase string.

```php
$req = new \\Core\Request();

echo $req->method(); // GET, POST, PUT, DELETE etc
```

The `posted()` method is a convenience helper that returns `true` only when the request method is `POST`.

```php
$req = new \\Core\Request();

if($req->posted())
{
	echo "Form submitted";
}
```

The `is_get()` method returns `true` when the current request is a `GET` request.

```php
$req = new \\Core\Request();

if($req->is_get())
{
	echo "Page loaded normally";
}
```

The `is_ajax()` method checks whether the request was made through AJAX by inspecting the `HTTP_X_REQUESTED_WITH` header.

```php
$req = new \\Core\Request();

if($req->is_ajax())
{
	echo "AJAX request detected";
}
```

These helpers are useful for separating normal page views from submitted forms or asynchronous requests.
