---
title: "Message Helper"
slug: "message-helper"
description: "Flash-style message helper."
published: true
order: 14
source_id: 65
keywords: ["message", "helper", "flash-style", "classes", "session", "class", "set", "retrieve"]
---

**Set a message**

```php
$session->message('success', 'Saved successfully');
```

**Retrieve a message**

```php
echo $session->message('success', '', true);
```
