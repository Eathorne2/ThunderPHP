---
title: "Custom Query Keys and Base URLs"
slug: "custom-query-keys-and-base-urls"
description: "How to use a different page parameter or force the pager to generate links from a custom base URL."
published: true
order: 11
source_id: 111
keywords: ["custom", "query", "keys", "base", "urls", "different", "page", "parameter", "force", "pager", "generate", "links", "url", "classes", "class"]
---

By default, the pager reads and writes the page number using the `page` query key.

If your route or frontend expects another key, use `set_page_key()`:

```php
$pager = new \Core\Pager(10, 2, $total_rows);
$pager->set_page_key('p');
```

This will make the pager read URLs like:

`?p=3`

If you want to force pagination links to use a specific base URL, use `set_base_url()`.

Example:

```php
$pager = new \Core\Pager(10, 2, $total_rows);

$pager->set_base_url(ROOT . '/admin/users?role=editor&status=active');
```

This is useful when:

- your current route is not based on `$_GET['url']`
- you want pagination inside a custom admin panel
- you want to preserve extra filters in the query string

Full URL example:

```php
$pager = new \Core\Pager(10, 2, $total_rows);

$pager->set_base_url('https://example.com/reports/sales?year=2026&region=north');
```

The pager will append or update the page value correctly.
