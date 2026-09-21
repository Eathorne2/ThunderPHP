---
title: "Validation in Real Plugins"
slug: "validation-in-real-plugins"
description: "Current Validate class capabilities, including hooks, custom rules, and unique constraints with where and whereNot."
published: true
order: 11
source_id: 234
keywords: ["validation", "real", "plugins", "current", "validate", "class", "capabilities", "including", "hooks", "custom", "rules", "unique", "constraints", "wherenot", "plugin", "reference", "refrence", "thunderphp", "developers", "important", "plugin-author", "features", "recommendation"]
---

ThunderPHP uses `\Core\Validate` for structured request validation.

The newer validation documentation shows support for:

- built-in scalar rules
- file and image validation
- custom rules through hooks
- static rule registration
- advanced unique rules with `where` and `whereNot` support

Example:

```php
$validate = new \Core\Validate($_POST);

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

**Important Plugin-Author Features**

- `firstError()` for field-specific display
- `fails()` for failure checks
- `validation_rules` filter for hook-based custom rules
- `\Core\Validate::extend()` for static rule registration
- `where` and `whereNot` for soft-delete-aware uniqueness logic

**Recommendation**

Prefer extending validation through hooks or registration methods instead of modifying the validator class directly.
