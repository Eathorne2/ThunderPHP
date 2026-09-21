# Run Action Hook

Runs a public ThunderPHP action hook from an execution flow. Other plugins can attach callbacks with `add_action()`.

```php
do_action('orders.saved', $data);
```

The Data input is optional and defaults to an empty array.
