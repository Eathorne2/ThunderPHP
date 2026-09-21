---
title: "Rule Definition Styles"
slug: "rule-definition-styles"
description: "Rules for how to validate user input can be written in a short form or a named form with user-friendly labels."
published: true
order: 1
source_id: 3
keywords: ["rule", "definition", "styles", "rules", "validate", "user", "input", "written", "short", "form", "named", "user-friendly", "labels", "validation", "simple", "style"]
---

You can define rules in more than one style.

**Simple style**

```php
$validate->setRules([
	'email' => ['required', 'email']
]);
```
**Named style**
```php
$validate->setRules([
	'email' => [
		'name' => 'Email address',
		'rules' => ['required', 'email']
	]
]);
```
The named style is better for user-facing forms because error messages can use a readable input name instead of the raw field key.
