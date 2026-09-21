---
title: "Total Rows and Total Pages"
slug: "total-rows-and-total-pages"
description: "Understanding total row count, total pages, and why these values matter."
published: true
order: 5
source_id: 105
keywords: ["total", "rows", "pages", "understanding", "row", "count", "why", "values", "matter", "classes", "pager", "class"]
---

Pagination works best when the pager knows how many total rows exist in the dataset.

The total row count allows the class to calculate:

- total number of pages
- whether a previous page exists
- whether a next page exists
- the correct last page
- accurate summary text such as `Showing 21-30 of 145`

Example:

```php
$total_rows = $db->get_row("select count(id) as num from products where published = 1")->num ?? 0;

$pager = new \Core\Pager(12, 2, $total_rows);

echo $pager->get_total_rows();
echo $pager->get_total_pages();
```

If you already created the pager and later obtained the count, you can set it afterward:

```php
$pager = new \Core\Pager(12, 2);

$total_rows = $db->get_row("select count(id) as num from products where published = 1")->num ?? 0;

$pager->set_total_rows((int)$total_rows);
```

This is useful if your count query and your pager initialization happen in different parts of the controller logic.
