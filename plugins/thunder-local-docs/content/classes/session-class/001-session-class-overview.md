---
title: "Session Class Overview"
slug: "session-class-overview"
description: "Introduction to the Session class and its purpose."
published: true
order: 1
source_id: 52
keywords: ["session", "class", "overview", "introduction", "purpose", "classes"]
---

The `Session` class provides a structured and convenient way to interact with PHP sessions in ThunderPHP. 

It abstracts direct access to `$_SESSION`, adds namespacing, and introduces advanced features such as:

- Dot notation for nested data
- Flash messages
- Old input handling
- Authentication helpers
- Array utilities

All session data is stored under a single root namespace:

```php
$_SESSION['THUNDER'];
```
