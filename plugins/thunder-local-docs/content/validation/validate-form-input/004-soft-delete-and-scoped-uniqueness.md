---
title: "Soft Delete and Scoped Uniqueness"
slug: "soft-delete-and-scoped-uniqueness"
description: "Use where and whereNot to make uniqueness checks respect soft deletes, school scopes, or custom filtering."
published: true
order: 4
source_id: 6
keywords: ["soft", "delete", "scoped", "uniqueness", "wherenot", "make", "checks", "respect", "deletes", "school", "scopes", "custom", "filtering", "form", "validation", "validate", "user", "input", "example"]
---

**Soft Delete Example**

```php
'email' => [
	'name' => 'Email address',
	'rules' => [
		'required',
		'email',
		[
			'rule' => 'unique',
			'param' => [
				'table' => 'users',
				'column' => 'email',
				'where' => [
					'deleted' => 0
				]
			]
		]
	]
]
```
Since soft deleted records still exist in the table, this checks uniqueness only among non-deleted rows.

*whereNot Example*

```php
'email' => [
	'name' => 'Email address',
	'rules' => [
		[
			'rule' => 'unique',
			'param' => [
				'table' => 'users',
				'column' => 'email',
				'whereNot' => [
					'active' => 0
				]
			]
		]
	]
]
```

This checks uniqueness only among rows where `active != 0`.

*Scoped Uniqueness Example*
```php
'email' => [
	'name' => 'Email address',
	'rules' => [
		[
			'rule' => 'unique',
			'param' => [
				'table' => 'users',
				'column' => 'email',
				'where' => [
					'school_id' => $school_id,
					'deleted' => 0
				],
				'whereNot' => [
					'status' => 'archived'
				]
			]
		]
	]
]
```
This is useful when uniqueness should only apply inside a specific scope such as a school(multi school website), tenant, company, or active-record set.
