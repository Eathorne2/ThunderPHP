---
title: "Unique Rule Options"
slug: "unique-rule-options"
description: "The advanced unique rule supports table targeting, ignored records, and scoped conditions."
published: true
order: 3
source_id: 5
keywords: ["unique", "rule", "options", "advanced", "supports", "table", "targeting", "ignored", "records", "scoped", "conditions", "form", "validation", "validate", "user", "input"]
---

The advanced `unique` rule supports the following keys:

- `table`
- `column`
- `primary_key`
- `ignore_value`
- `ignore_from_data`
- `where`
- `whereNot`

*Explanation of each parameter:*

`table`

- Database table name in which to search for the unique value.

```php
'table' => 'users'
```

`column`

- Database column name. If omitted, the current field name is used e.g name of your form input.

```php
'column' => 'email'
```

`primary_key`

- The primary key column used when ignoring the current record during updates. e.g if updating a user record from a form and email is used as unique identifier, the validator will ignore the current user's record when checking for duplicates

```php
'primary_key' => 'user_id'
```

`ignore_value`

- Explicitly sets the record ID to ignore.

```php
'ignore_value' => '12'
```

`ignore_from_data`

- Loads the ignore value from the validation data.

```php
'ignore_from_data' => 'user_id'
```

`where`

- Adds equality conditions. i.e a where clause in the query when searching for duplicates in the table

```php
'where' => [
	'deleted' => 0,
	'school_id' => $school_id
]
```

`whereNot`

- Adds inequality conditions.

```php
'whereNot' => [
	'active' => 0,
	'status' => 'archived'
]
```
