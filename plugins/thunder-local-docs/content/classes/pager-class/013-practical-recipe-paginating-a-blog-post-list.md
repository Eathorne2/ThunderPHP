---
title: "Practical Recipe: Paginating a Blog Post List"
slug: "practical-recipe-paginating-a-blog-post-list"
description: "Recipe for counting posts, querying a single page of results, and showing the pager."
published: true
order: 13
source_id: 113
keywords: ["practical", "recipe", "paginating", "blog", "post", "list", "counting", "posts", "querying", "single", "page", "results", "showing", "pager", "classes", "class"]
---

This recipe shows a standard blog listing page.

Step 1: get the total count.

Step 2: create the pager.

Step 3: use `limit()` and `offset()` in the query.

Step 4: display the links.

```php
$count_row = $db->get_row("select count(id) as num from posts where published = 1");
$total_rows = $count_row->num ?? 0;

$pager = new \Core\Pager(10, 2, (int)$total_rows);

$rows = $db->query("
    select *
    from posts
    where published = 1
    order by id desc
    limit {$pager->limit()} offset {$pager->offset()}
");

if(!empty($rows)){
    foreach($rows as $row){
        echo '<h3>' . esc($row->title) . '</h3>';
        echo '<p>' . esc($row->excerpt) . '</p>';
    }
}

$pager->display();
```

This is one of the most common real-world uses of the class.
