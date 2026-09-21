---
title: "Reading Server Data and Headers"
slug: "reading-server-data-and-headers"
description: "Using `server()`, `header()`, `ip()`, and `uri()` for request metadata."
published: true
order: 16
source_id: 82
keywords: ["reading", "server", "data", "headers", "header", "ip", "uri", "request", "metadata", "classes", "class", "get", "client", "current", "read", "one", "value", "all"]
---

The `Request` class also provides a few helpers for request metadata.

**Get the client IP**

```php
$req = new \\Core\Request();

echo $req->ip();
```

**Get the current request URI**

```php
$req = new \\Core\Request();

echo $req->uri();
```

**Read one server value**

```php
$req = new \\Core\Request();

$agent = $req->server('HTTP_USER_AGENT');
echo $agent;
```

**Read all server data**

```php
$req = new \\Core\Request();

print_r($req->server());
```

**Read a request header**

```php
$req = new \\Core\Request();

$token = $req->header('Authorization');
echo $token;
```

This header method converts names like `Authorization` into the corresponding `$_SERVER` style key internally.

These helpers are useful for:

- logging
- debugging
- middleware-style checks
- AJAX detection
- API token handling
- route diagnostics

They help keep your controller code cleaner and reduce direct access to `$_SERVER`.
