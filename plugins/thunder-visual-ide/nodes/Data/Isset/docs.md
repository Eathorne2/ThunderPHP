# Isset Check

Outputs the boolean result of PHP `isset()` for a variable or nested value.

Use a variable path without the leading `$`:

- `user` compiles to `isset($user)`
- `user.profile.email` in array mode compiles to `isset($user['profile']['email'])`
- `user.profile.email` in object mode compiles to `isset($user->profile->email)`
- `items.0.id` in array mode compiles to `isset($items[0]['id'])`

`isset()` returns `false` when the target is missing or its value is `null`.
