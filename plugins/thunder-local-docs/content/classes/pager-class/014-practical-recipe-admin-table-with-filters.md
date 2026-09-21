---
title: "Practical Recipe: Admin Table with Filters"
slug: "practical-recipe-admin-table-with-filters"
description: "Recipe for preserving filters and paginating an admin table using a custom base URL."
published: true
order: 14
source_id: 114
keywords: ["practical", "recipe", "admin", "table", "filters", "preserving", "paginating", "custom", "base", "url", "classes", "pager", "class"]
---

In an admin page, you often want pagination links to preserve filters such as role, status, search, or date range.

This is a good case for `set_base_url()`.

```php
$role = $_GET['role'] ?? 'editor';
$status = $_GET['status'] ?? 'active';

$count_row = $db->get_row("
    select count(id) as num
    from users
    where role = :role and status = :status
",[
    'role' => $role,
    'status' => $status,
]);

$total_rows = $count_row->num ?? 0;

$pager = new \Core\Pager(20, 2, (int)$total_rows);

$pager->set_base_url(ROOT . '/admin/users?role=' . urlencode($role) . '&status=' . urlencode($status));

$rows = $db->query("
    select *
    from users
    where role = :role and status = :status
    order by id desc
    limit {$pager->limit()} offset {$pager->offset()}
",[
    'role' => $role,
    'status' => $status,
]);

if(!empty($rows)){
    foreach($rows as $row){
        echo '<div>' . esc($row->email) . '</div>';
    }
}

$pager->display();
```

This keeps the selected filter values in all pagination links.
