# Hook Data Argument

References the argument passed into the current **Controller Hook** callback.

If the controller uses the default callback variable, the node outputs:

```php
$data
```

If the controller's **Callback data variable** is `items`, the same node automatically outputs:

```php
$items
```

The optional property/key path works like the normal Variable node:

- Object path `user.email` becomes `$data->user->email`.
- Array path `user.email` becomes `$data['user']['email']`.

This node does not create or copy the callback data. It references the original supplied argument, so changes made through normal flow assignments can be returned by a filter.
