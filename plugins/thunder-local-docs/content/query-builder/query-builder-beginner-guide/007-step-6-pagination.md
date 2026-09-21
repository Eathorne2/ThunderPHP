---
title: "Step 6: Pagination"
slug: "step-6-pagination"
description: "Paginate large result sets and display page information."
published: true
order: 7
source_id: 39
keywords: ["step", "pagination", "paginate", "large", "result", "sets", "display", "page", "information", "query", "builder", "beginner", "guide"]
---

Pagination is useful when you have many records and want to display them in smaller pages.

```php
$page = $_GET['page'] ?? 1;

$results = $userModel->paginate(10, $page);
```

Loop through the rows:

```php
foreach ($results['data'] as $user) {
    echo $user->name;
}
```

Show page information:

```php
echo "Page {$results['current_page']} of {$results['last_page']}";
```
