---
title: "Custom Error Messages and Reading Errors"
slug: "custom-error-messages-and-reading-errors"
description: "Override specific messages and retrieve validation failures in different ways."
published: true
order: 6
source_id: 8
keywords: ["custom", "error", "messages", "reading", "errors", "override", "specific", "retrieve", "validation", "failures", "different", "ways", "form", "validate", "user", "input", "get", "all", "first", "one", "field", "check", "failure"]
---

**Custom Error Messages**

- You can override error messages for individual rules.

```php
'email' => [
	'name' => 'Email address',
	'rules' => [
		'required',
		[
			'rule' => 'email',
			'error_message' => 'Please enter a valid email address'
		]
	]
]
```
**Get all errors**

```php
$errors = $validate->getErrors();
```

**Get first error for one field**

```php
$error = $validate->firstError('email');
```

**Check failure**

```php
if($validate->fails())
{
	// handle errors
}
```
