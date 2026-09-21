---
title: "Reading GET Data"
slug: "reading-get-data"
description: "How to read query string values using `get()` and work with defaults."
published: true
order: 4
source_id: 70
keywords: ["reading", "get", "data", "read", "query", "string", "values", "work", "defaults", "classes", "request", "class"]
---

Use `get()` when you want to read values from the URL query string.

With no key, it returns the full `$_GET` array.

```php
$req = new \\Core\Request();

$params = $req->get();
print_r($params);
```

With a key, it returns one value.

```php
$req = new \\Core\Request();

$page = $req->get('page', 1);
$search = $req->get('search', '');
```

This is useful for:

- pagination
- filters
- search terms
- sort order
- category selection

Example:

```php
$req = new \\Core\Request();

$page = (int)$req->get('page', 1);
$sort = $req->get('sort', 'latest');
$keyword = trim($req->get('q', ''));

echo $page;
echo $sort;
echo $keyword;
```

Just like `post()`, this method preserves valid values such as `0` because it checks whether the key exists rather than treating empty values as missing.
