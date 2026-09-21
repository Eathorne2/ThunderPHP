# Empty Check

Outputs the boolean result of PHP `empty()` for either a connected value or a configured variable path.

## Connected value

Connect any PHP expression to **Value** to compile a direct check:

```php
empty($value)
empty(get_value('search'))
empty($request->post('email'))
```

The connected input takes priority over the inspector path.

## Variable path fallback

When **Value** is disconnected, use a variable path without the leading `$`:

- `search` compiles to `empty($search)`
- `form.email` in array mode compiles to `empty($form['email'])`
- `user.profile` in object mode compiles to `empty($user->profile)`
- `items.0.name` in array mode compiles to `empty($items[0]['name'])`

PHP considers values such as `null`, `false`, `0`, `"0"`, an empty string and an empty array to be empty. Missing variable-path targets also return `true` without an undefined-variable warning.
