---
title: "Admin Check"
slug: "admin-check"
description: "Check if a user has admin privileges."
published: true
order: 8
source_id: 59
keywords: ["admin", "check", "user", "privileges", "classes", "session", "class", "status"]
---

**Check admin status**

```php
if($session->is_admin()){
    echo "Admin access";
}
```

This uses the `before_check_admin` filter internally.

Preferably, use the `contains_role('admin')` helper function. 

```php
if(contains_role('admin')){
    echo "Admin access";
}
```
This uses the `user_roles` filter internally.
