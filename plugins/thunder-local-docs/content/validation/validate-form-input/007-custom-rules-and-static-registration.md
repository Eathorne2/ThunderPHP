---
title: "Custom Rules and Static Registration"
slug: "custom-rules-and-static-registration"
description: "Extend validation through hooks or static rule registration without editing the validator core."
published: true
order: 7
source_id: 9
keywords: ["custom", "rules", "static", "registration", "extend", "validation", "through", "hooks", "rule", "without", "editing", "validator", "core", "form", "validate", "user", "input", "like"]
---

**Custom rules through hooks**

Plugins can register custom validation rules using the `validation_rules` filter. This is an example of adding a custom phone number validation. In this case a Zambian phone number.

```php
add_filter('validation_rules', function($rules){

	$rules['phone_zm'] = function($field, $value, $param, $meta, $validator){

		if($validator->isEmpty($value)) return;

		if(!preg_match('/^(?:\+260|0)(95|96|97)\d{7}$/', (string)$value))
		{
			$validator->addError(
				$field,
				!empty($meta['error_message']) ? $meta['error_message'] : $meta['input_name']." is not a valid Zambian mobile number"
			);
		}
	};

	return $rules;
});
```

**Use the rule like this:**

```php
$validate->setRules([
	'phone' => [
		'name' => 'Phone number',
		'rules' => ['required', 'phone_zm']
	]
]);
```

**Static rule registration**

```php
\\Core\Validate::extend('slug', function($field, $value, $param, $meta, $validator){

	if($validator->isEmpty($value)) return;

	if(!preg_match('/^[a-z0-9\-]+$/', (string)$value))
	{
		$validator->addError(
			$field,
			!empty($meta['error_message']) ? $meta['error_message'] : $meta['input_name']." is not a valid slug"
		);
	}
});
```

Both approaches let you extend validation cleanly without changing the core validator file.
