# Validate

Validates a connected data array through `Core\Validate` and branches on success or failure.

The inspector includes a visual rules manager. Add a data field, choose one or more documented rules, configure any required parameters, and optionally set a display name or custom error message.

Built-in rule definitions are loaded from `validation-rules/rules.json`, so the list can be extended without changing the node compiler. Custom rules registered by the application can be entered through **Custom rule**.

The manager writes the normal rules array passed to:

```php
$validator->setRules([...]);
```

Use **Edit Raw JSON** for uncommon or framework-specific structures.
