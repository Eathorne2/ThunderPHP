---
title: "Flash Messages"
slug: "flash-messages"
description: "Temporary session messages."
published: true
order: 10
source_id: 61
keywords: ["flash", "messages", "temporary", "session", "classes", "class", "set", "message", "retrieve", "check", "clear", "all", "data"]
---

**Set a flash message**

```php
$session->flash('success', 'Saved successfully');
```

**Retrieve a flash message**

```php
echo $session->get_flash('success');
```

**Check for a flash message**

```php
if($session->has_flash('error')){
    echo $session->get_flash('error');
}
```

**Clear all flash data**

```php
$session->clear_flash();
```
