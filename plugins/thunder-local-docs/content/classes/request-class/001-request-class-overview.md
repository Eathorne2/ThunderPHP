---
title: "Request Class Overview"
slug: "request-class-overview"
description: "Introduction to the Request class, what it does, and the main areas it covers."
published: true
order: 1
source_id: 67
keywords: ["request", "class", "overview", "introduction", "does", "main", "areas", "covers", "classes"]
---

The `Request` class is the central helper for reading incoming HTTP request data and handling uploaded files in one place.

It is designed to give you a clean way to work with:

- request methods such as `GET` and `POST`
- form input from `$_POST`
- query string values from `$_GET`
- uploaded files from `$_FILES`
- request metadata from `$_SERVER`
- safe and configurable file uploads

This class is especially useful because it keeps your controllers and plugin files cleaner. Instead of reading directly from PHP superglobals everywhere, you can create one `Request` instance and access everything from there.

A typical usage example looks like this:

```php
$req = new \\Core\Request();

if($req->posted())
{
	$name = $req->post('name');
	$email = $req->post('email');
}
```

The upload system also supports:

- single file uploads
- multiple file uploads using one input like `photos[]`
- per-field upload rules
- MIME type validation
- file extension validation
- optional image verification
- optional image dimension limits
- grouped upload results

In most cases, the `Request` class should be created inside a controller or plugin controller and then used as needed throughout the request lifecycle.
