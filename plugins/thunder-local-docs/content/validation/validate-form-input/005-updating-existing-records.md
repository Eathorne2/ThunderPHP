---
title: "Updating Existing Records"
slug: "updating-existing-records"
description: "Ignore the current record during unique checks when editing rows."
published: true
order: 5
source_id: 7
keywords: ["updating", "existing", "records", "ignore", "current", "record", "during", "unique", "checks", "editing", "rows", "form", "validation", "validate", "user", "input"]
---

When editing a record, the validator can ignore the current row so the existing value does not fail the uniqueness check.

*Example data*

```php
$data = [
	'id' => 10,
	'email' => 'admin@example.com'
];
```

If your table uses another primary key:
```php
$validate->primaryKey = 'user_id';
```

Or set it directly in the rule:

```php
[
	'rule' => 'unique',
	'param' => [
		'table' => 'users',
		'primary_key' => 'user_id',
		'ignore_from_data' => 'user_id'
	]
]
```
This is especially important for update forms where the record already owns the current value.
