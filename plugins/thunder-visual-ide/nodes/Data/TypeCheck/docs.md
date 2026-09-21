# Type Check

Checks a connected PHP value with a selected built-in predicate and outputs a boolean.

Available checks include:

- `is_numeric`
- `is_string`
- `is_int`
- `is_float`
- `is_bool`
- `is_array`
- `is_object`
- `is_null`
- `is_scalar`
- `is_iterable`
- `is_countable`
- `is_callable`
- `is_resource`

For example, selecting `is_numeric` compiles to:

```php
is_numeric($value)
```

The node accepts any connected PHP expression. An unconnected input falls back to `null`.
