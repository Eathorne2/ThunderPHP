---
title: "Limit, Offset, and SQL Integration"
slug: "limit-offset-and-sql-integration"
description: "How to use the pager with SQL queries and why limit and offset matter."
published: true
order: 4
source_id: 104
keywords: ["limit", "offset", "sql", "integration", "pager", "queries", "why", "matter", "classes", "class"]
---

The most important practical use of the pager is generating the correct SQL `limit` and `offset` values.

- `limit()` returns how many rows to fetch
- `offset()` returns how many rows to skip

Example:

```php
$total_rows = $db->get_row("select count(id) as num from posts")->num ?? 0;

$pager = new \Core\Pager(10, 2, $total_rows);

$rows = $db->query("select * from posts order by id desc limit {$pager->limit()} offset {$pager->offset()}");
```

If the current page is `1` and the limit is `10`:

- `limit()` returns `10`
- `offset()` returns `0`

If the current page is `3` and the limit is still `10`:

- `limit()` returns `10`
- `offset()` returns `20`

That is why page 3 starts at row 21 in the overall dataset.

There are also getter equivalents:

- `get_limit()`
- `get_offset()`

Example:

```php
$pager = new \Core\Pager(25, 1, $total_rows);

$limit = $pager->get_limit();
$offset = $pager->get_offset();

$rows = $db->query("select * from users limit $limit offset $offset");
```

Use whichever naming style you prefer in your project.
