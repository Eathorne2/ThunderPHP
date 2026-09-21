---
title: "Basic Usage"
slug: "basic-usage"
description: "Create a validator instance, define rules, run validation, and retrieve errors."
published: true
order: 0
source_id: 2
keywords: ["basic", "usage", "create", "validator", "instance", "define", "rules", "run", "validation", "retrieve", "errors", "form", "validate", "user", "input"]
---

At the top of your page.
```php
use \\Core\Validate;
use \\Core\Request;

$req = new Request;

```
`$req->post()` is the same as `$_POST`. 

Create a new validator using your incoming data, then define rules with `setRules()`. 
```php
$validate = new Validate($req->post());
$validate->setRules([
	'username' => [
		'name' => 'Username',
		'rules' => ['required', 'alpha_numeric', 'min:3', 'max:20']
	],
	'email' => [
		'name' => 'Email address',
		'rules' => ['required', 'email']
	],
]);

if($validate->validate())
{
	// valid
}
else
{
	$errors = $validate->getErrors();
}
```
This is the standard validation flow
- collect request data
- create validator
- define rules
- run validation
- handle errors if validation fails
