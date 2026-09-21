---
title: "Step 1: Creating a Model"
slug: "step-1-creating-a-model"
description: "Create a model before using the Query Builder."
published: true
order: 2
source_id: 34
keywords: ["step", "creating", "model", "create", "before", "query", "builder", "beginner", "guide"]
---

Before using the Query Builder, you will usually work through a model.

```php
namespace Model;

class User extends Model
{
    protected string $table = 'users';
}
```

Now you can use it like this:

```php
$userModel = new User;
```
