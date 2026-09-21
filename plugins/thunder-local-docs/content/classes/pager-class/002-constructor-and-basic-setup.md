---
title: "Constructor and Basic Setup"
slug: "constructor-and-basic-setup"
description: "How to instantiate the pager and what each constructor argument means."
published: true
order: 2
source_id: 102
keywords: ["constructor", "basic", "setup", "instantiate", "pager", "each", "argument", "means", "classes", "class"]
---

The `Pager` constructor accepts three arguments:

- items per page
- extra page numbers to show around the current page
- total number of rows

Signature:

```php
$pager = new \Core\Pager($limit, $extras, $total_rows);
```

Parameters:

- `limit`: how many records should be shown per page
- `extras`: how many page numbers should appear on each side of the current page
- `total_rows`: the total number of matching records in the dataset

Example:

```php
$pager = new \Core\Pager(20, 2, 480);
```

In this example:

- 20 records are shown per page
- 2 page numbers are shown before and after the current page
- the dataset contains 480 rows

If the third argument is omitted, the pager can still work, but features such as correctly disabling the final navigation buttons are less precise. In normal usage, it is best to provide the total row count.

```php
$pager = new \Core\Pager(10, 1);
```

The pager automatically reads the current page from the query string using the `page` key by default.
