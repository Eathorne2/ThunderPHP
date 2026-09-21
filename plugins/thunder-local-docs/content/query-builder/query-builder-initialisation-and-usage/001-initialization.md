---
title: "Initialization"
slug: "initialization"
description: "The query builder can be initialized in two ways."
published: true
order: 1
source_id: 11
keywords: ["initialization", "query", "builder", "initialized", "two", "ways", "initialisation", "usage", "direct", "model", "recommended"]
---

**1. Direct Usage**
- Through the `\\Core\Database` class. Using this method means you always have to define the database table you're targeting. 

```php
$qb = new \\Core\Database;
$users = $qb->table('users')->get();
```


**2. Using a Model (Recommended)**
- This method is better because you skip the table selection and also keep the ability to add your own methods to the Model.

*Create a Model*
- Models must always live in the models folder of your plugin i.e `plugin-folder/models/`.

```php
use \Model\Model;
class User extends Model
{
    protected string $table = 'users';
}
```

*Usage*

```php
$userModel = new User;
$users = $userModel->where('active', 1)->get();
```
The above code will query all active users in the users table. The table name is already defined in the class and so theres no need to specify it in the query building process.
