# Class Definition

Creates `classes/<ClassName>.php` in the generated plugin and loads it from `plugin.php`.

Use **Manage Methods** to define visibility, static state, optional parameter types, optional return type, Markdown help, and one execution graph per method. Add a method named `__construct` to define constructor parameters and logic.

The **Help information** field supports Markdown, including fenced PHP code examples. Class Method Call nodes display it in a searchable two-column Method Browser so users can understand a method before selecting it.

Class methods may use either a nested execution graph or a typed PHP body. The Method Flow button saves the method and opens its graph directly.

Method parameters may define an optional raw PHP default value such as `null`, `[]`, `false`, `10`, or `'draft'`. Call nodes omit trailing unconnected optional parameters so PHP uses the declared defaults.

## Chainable custom methods

Enable **Can continue a method chain** when a custom method returns the current class instance and another method may be called on that result. The IDE stores this as `"chainable": true` in the method definition and marks the method in the graph and Method Browser.

A typical chainable method should return `$this` or another current-class expression:

```php
public function configure(string $key, mixed $value): self
{
    $this->settings[$key] = $value;
    return $this;
}
```

Do not enable it for methods returning arrays, strings, booleans, row data, or other terminal results unless the returned object genuinely supports the next methods.
