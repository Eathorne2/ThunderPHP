# Run Filter Hook

Runs a public ThunderPHP filter and returns the modified value. Other plugins can attach callbacks with `add_filter()`.

```php
$filteredData = do_filter('orders.before_save', $data);
```

Connect **Filtered Value** to later nodes. The Data input defaults to an empty array.
