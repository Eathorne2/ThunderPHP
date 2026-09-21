---
title: "Paginate the Post List"
slug: "paginate-the-post-list"
description: "Use the current Pager class to keep list pages in sync with the query."
published: true
order: 12
source_id: 251
keywords: ["paginate", "post", "list", "current", "pager", "class", "keep", "pages", "sync", "query", "plugin", "dev", "tutorials", "build", "first"]
---

You can use the dedicated Pager class directly, or use query builder pagination metadata where that fits better.

Direct Pager example:

```php
$total_rows = 125;
$pager = new \Core\Pager(10, 2, $total_rows);

$rows = $db->query("select * from basic_blog_posts limit {$pager->limit()} offset {$pager->offset()}");

foreach($rows as $row)
{
    echo $row->title . "<br>";
}

$pager->display();
```

The current Pager supports `render()`, `display()`, `has_prev()`, `has_next()`, current page detection from the query string, and custom layout strategies when you do not want the default HTML output.
