---
title: "Built-in Rules"
slug: "built-in-rules"
description: "ThunderPHP includes common validation rules for strings, numbers, choices, comparisons, files, and uploads.\n\nBelow are the built-in rules supported by the validator."
published: true
order: 2
source_id: 4
keywords: ["built-in", "rules", "thunderphp", "includes", "common", "validation", "strings", "numbers", "choices", "comparisons", "files", "uploads", "below", "supported", "validator", "form", "validate", "user", "input", "required", "nullable", "email", "url", "numeric"]
---

**1. required**
- The field must not be empty.

```php
'required'
```

**2. nullable**
- Marker rule for optional fields. It does not raise errors by itself.

```php
'nullable'
```

**3. email**
- The field must contain a valid email address.

```php
'email'
```

**4. url**
- The field must contain a valid URL.

```php
'url'
```

**5. numeric**
- The field must be numeric.

```php
'numeric'
```

**6. integer**
- The field must be a whole number.

```php
'integer'
```

**7. min**
- The field must have at least the given character length.

```php
'min:3'
```

**8. max**
- The field must not exceed the given character length.

```php
'max:100'
```

**9. min_value**
- The numeric value must be at least the given amount.

```php
'min_value:10'
```

**10. max_value**
- The numeric value must not exceed the given amount.

```php
'max_value:100'
```

**11. date**
- The field must be a valid date string.

```php
'date'
```

**12. match**
- The field must match another field.

```php
'match:password'
```

**13. alpha**
- The field may contain letters and spaces only.

```php
'alpha'
```

**14. alpha_numeric**
- The field may contain letters, numbers, and spaces only.

```php
'alpha'
```

**15. no_space**
- The field must not contain spaces.

```php
'no_space'
```

**16. in**
- The value must be one of the allowed values.

```php
'in:admin,user,editor'
```
*Array form:*
```php
[
	'rule' => 'in',
	'param' => ['admin', 'user', 'editor']
]
```

**17. not_in**
- The value must not be one of the blocked values.

```php
'not_in:banned,deleted'
```

**18. regex**
- The value must match a full regex pattern.

```php
'regex:/^[A-Z0-9]+$/'
```

**19. required_if**
- The field becomes required when another field has a given value.

```php
'required_if:account_type,business'
```

**20. file**
- Validates that an uploaded file exists and was uploaded without errors.

```php
'file'
```

**21. image**
- Validates that an uploaded file is an image.

```php
'image'
```

**22. boolean**
- The value must be a recognized boolean-like value.

```php
'boolean'
```

**23. unique**
- Checks uniqueness in the database.

```php
'unique:users'
```

*Advanced Form:*
```php
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
```
