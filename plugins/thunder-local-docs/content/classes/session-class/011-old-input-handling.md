---
title: "Old Input Handling"
slug: "old-input-handling"
description: "Persist form data between requests."
published: true
order: 11
source_id: 62
keywords: ["old", "input", "handling", "persist", "form", "data", "between", "requests", "classes", "session", "class", "store", "retrieve", "stored", "value", "shortcut", "method", "clear"]
---

**Store form data**

```php
$session->set_old($_POST);
```

**Retrieve a stored value**

```php
$email = $session->get_old('email', '');
```

**Use the shortcut method**

```php
$value = $session->old('email');
```

**Clear old input**

```php
$session->clear_old();
```
