---
title: "URL and HTTP Helpers"
slug: "url-and-http-helpers"
description: "Functions for reading the current URL, generating internal links, and working with URL segments."
published: true
order: 2
source_id: 118
keywords: ["url", "http", "helpers", "functions", "reading", "current", "generating", "internal", "links", "working", "segments", "thunderphp", "list", "covered", "current_url", "base_url", "string", "path", "split_url", "key", "page", "redirect", "recipe", "back"]
---

**Functions covered**

- `current_url()`
- `base_url()`
- `split_url()`
- `URL()`
- `page()`
- `redirect()`

**`current_url()`**

Returns the full current URL, including protocol, host, path, and query string.

```php
$link = current_url();
```

Useful for refresh links, return URLs, or debugging.

**`base_url(string $path = '')`**

Prepends the application root URL to a relative path.

```php
echo base_url('admin/users');
echo base_url('assets/images/logo.png');
```

**`split_url(string $url)`**

Splits a URL string into path segments.

```php
$parts = split_url('/blog/post/hello-world');
// ['blog', 'post', 'hello-world']
```

This is mostly a low-level helper and is usually more useful internally than in plugin code.

**`URL(string $key = '')`**

Returns URL segments prepared by the framework.

```php
echo URL(0); // first segment
echo URL(1); // second segment
```

If no key is supplied, it returns the full URL segment array.

```php
$segments = URL();
```

**`page()`**

Shortcut for `URL(0)`.

```php
if(page() == 'admin') {
    // admin area
}
```

**`redirect(string $url)`**

Redirects to an internal path under `ROOT`.

```php
redirect('admin/posts');
```

Because it sends a `Location` header and stops execution, call it before any output is sent.

**Recipe: redirect back to a plugin page**

```php
$vars = get_value();

redirect($vars['admin_route'] . '/' . $vars['plugin_route']);
```
