---
title: "Database and Query Builder in Plugins"
slug: "database-and-query-builder-in-plugins"
description: "How plugin authors should use the Database and newer QueryBuilder features."
published: true
order: 9
source_id: 232
keywords: ["database", "query", "builder", "plugins", "plugin", "authors", "newer", "querybuilder", "features", "reference", "refrence", "thunderphp", "developers", "basics", "updated", "why", "matters", "docs"]
---

ThunderPHP plugins can use the database in two broad ways:

- raw SQL through `\Core\Database`
- fluent query building through the updated QueryBuilder API

**Database Basics**

```php
$db = new \Core\Database;
$rows = $db->query("select * from posts where active = :active", ['active' => 1]);
$row = $db->get_row("select * from posts where id = :id limit 1", ['id' => 5]);
```

Useful properties include:

- `has_error`
- `error`
- `insert_id`
- `affected_rows`
- static `query_id` for after-query customization

**Updated Query Builder**

The newer query builder supports fluent select, insert, update, delete, joins, grouped conditions, aggregates, pagination metadata, unions, raw expressions, subqueries, upserts, and error tracking. Results are returned as an array of objects, a single object, or `null` depending on the query path.

Example:

```php
$postModel = new Post;

$posts = $postModel
    ->select('id', 'title', 'slug')
    ->where('published', 1)
    ->orderBy('date_created', 'DESC')
    ->paginate(20, $_GET['page'] ?? 1);
```

**Why This Matters For Plugin Docs**

Older ThunderPHP docs that treat the database layer as mostly raw SQL are incomplete now. Plugin authors can build much richer plugin data access with the updated builder API and should be encouraged to do so where it improves clarity.
