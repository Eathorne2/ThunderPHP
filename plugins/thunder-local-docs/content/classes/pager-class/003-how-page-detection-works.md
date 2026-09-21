---
title: "How Page Detection Works"
slug: "how-page-detection-works"
description: "Explanation of how the current page is detected and sanitized."
published: true
order: 3
source_id: 103
keywords: ["page", "detection", "works", "explanation", "current", "detected", "sanitized", "classes", "pager", "class"]
---

By default, the pager checks the query string for a parameter called `page`.

Example URL:

`https://example.com/posts?page=3`

In this case, the pager will treat page `3` as the current page.

Invalid or missing values are sanitized automatically:

- empty value becomes page `1`
- page numbers below `1` become page `1`

This means the following URLs all safely resolve to page `1`:

- `?page=`
- `?page=0`
- `?page=-9`

Example:

```php
$pager = new \Core\Pager(15, 2, $total_rows);

echo $pager->current_page();
```

This is useful when you need to show the current page number inside the interface or use it in conditional logic.

If you want the pager to use another query string key instead of `page`, you can change it with `set_page_key()`.

```php
$pager = new \Core\Pager(15, 2, $total_rows);
$pager->set_page_key('p');

echo $pager->current_page();
```
