---
title: "CSRF Protection"
slug: "csrf-protection"
description: "Reference for generating and verifying CSRF tokens using `csrf()` and `csrf_verify()`."
published: true
order: 11
source_id: 127
keywords: ["csrf", "protection", "reference", "generating", "verifying", "tokens", "csrf_verify", "functions", "thunderphp", "list", "covered", "seskey", "hours", "return_input", "true", "token", "lifetime", "post", "custom", "session", "key", "recipe", "csrf-safe", "delete"]
---

**Functions covered**

- `csrf()`
- `csrf_verify()`

CSRF tokens protect forms from cross-site request forgery.

**`csrf($sesKey = 'csrf', $hours = 1, $return_input = true)`**

Creates a token and stores it in session.

By default it returns a full hidden input element.

```php
<form method="post">
    <?= csrf() ?>
    <input type="text" name="title">
    <button>Save</button>
</form>
```

You can also request the raw token string.

```php
$token = csrf('csrf', 1, false);
```

**Token lifetime**

The token remains valid for the configured number of hours.

**`csrf_verify($post, $sesKey = 'csrf')`**

Verifies a token submitted by a form.

```php
if(!csrf_verify($_POST)) {
    message('fail', 'Invalid CSRF token');
}
```

Verified tokens are removed after successful use, which makes them one-time tokens.

**Using a custom session key**

```php
<?= csrf('delete_csrf') ?>
```

```php
if(!csrf_verify($_POST, 'delete_csrf')) {
    message('fail', 'Invalid delete request');
}
```

**Recipe: CSRF-safe delete form**

```php
<form method="post">
    <?= csrf('delete_csrf') ?>
    <input type="hidden" name="id" value="<?= esc($row->id) ?>">
    <button type="submit">Delete</button>
</form>
```

```php
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(!csrf_verify($_POST, 'delete_csrf')) {
        message('fail', 'Invalid request');
        return;
    }

    // delete logic
}
```
