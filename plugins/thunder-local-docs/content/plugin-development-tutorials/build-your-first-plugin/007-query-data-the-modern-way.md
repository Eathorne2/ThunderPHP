---
title: "Query Data the Modern Way"
slug: "query-data-the-modern-way"
description: "Use the updated query builder or Database class to fetch and save plugin data."
published: true
order: 7
source_id: 246
keywords: ["query", "data", "modern", "way", "updated", "builder", "database", "class", "fetch", "save", "plugin", "dev", "tutorials", "build", "first"]
---

Use the updated query builder where it improves clarity.

Example list query:

```php
$postModel = new Post;

$posts = $postModel
    ->select('id', 'title', 'slug', 'date_created')
    ->where('published', 1)
    ->orderBy('date_created', 'DESC')
    ->paginate(10, $_GET['page'] ?? 1);
```

Example single post:

```php
$post = $postModel
    ->where('slug', get_param('slug'))
    ->where('published', 1)
    ->first();
```

The updated builder supports joins, nested conditions, raw clauses, pagination metadata, aggregates, unions, upserts, and more, so plugin authors no longer need to fall back to raw SQL for every non-trivial query.
