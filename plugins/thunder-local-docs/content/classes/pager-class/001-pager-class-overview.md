---
title: "Pager Class Overview"
slug: "pager-class-overview"
description: "Introduction to the Pager class, what it does, and when to use it."
published: true
order: 1
source_id: 101
keywords: ["pager", "class", "overview", "introduction", "does", "classes"]
---

The `Pager` class is ThunderPHP's general-purpose pagination utility. It helps you split large datasets into pages, calculate the correct SQL offset, generate page links, and render pagination controls in HTML.

Use the pager whenever you are listing records such as blog posts, users, products, comments, logs, media items, or any other dataset that should be displayed in chunks instead of all at once.

The class is designed to handle three main jobs:

- determine the current page
- calculate pagination boundaries
- generate pagination links and output

In most cases, the flow looks like this:

1. create a pager instance
2. pass the total row count if available
3. use `limit()` and `offset()` in your SQL query
4. display the pagination controls under the results

```php
$total_rows = 125;

$pager = new \Core\Pager(10, 2, $total_rows);

$rows = $db->query("select * from posts limit {$pager->limit()} offset {$pager->offset()}");

foreach($rows as $row){
    echo $row->title . "<br>";
}

$pager->display();
```

This makes sure the database query and the pager stay in sync.
