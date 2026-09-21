# Unset Variable

Removes a PHP variable or nested array value in a flow graph.

Use dot notation without the leading `$`:

- `temporary` compiles to `unset($temporary);`
- `user.profile.avatar` compiles to `unset($user['profile']['avatar']);`
- `items.0.price` compiles to `unset($items[0]['price']);`

The node intentionally uses a fixed inspector path because PHP `unset()` requires an assignable variable target rather than an arbitrary runtime expression.
