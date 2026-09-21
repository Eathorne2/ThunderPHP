# Refresh

Redirects the browser to ThunderPHP's `current_url()` and then stops PHP execution.

Generated code:

```php
redirect(current_url());
exit;
```

Use it after saving, deleting, or changing page state when the current route should reload.
