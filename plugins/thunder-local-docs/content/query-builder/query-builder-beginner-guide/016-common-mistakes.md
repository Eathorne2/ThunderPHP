---
title: "Common Mistakes"
slug: "common-mistakes"
description: "Avoid a few common beginner mistakes when using the builder."
published: true
order: 16
source_id: 48
keywords: ["common", "mistakes", "avoid", "few", "beginner", "builder", "query", "guide"]
---

Forgetting `get()`:

```php
$userModel->where('status', 'active');
```

The query above only builds the query. It does not execute it.

Correct version:

```php
$userModel->where('status', 'active')->get();
```

Mixing array and object access:

```php
echo $user['name'];
```

Correct version:

```php
echo $user->name;
```

Updating without a `where()` clause:

```php
$userModel->update(['status' => 'inactive']);
```

That updates every row in the table, so be careful.
