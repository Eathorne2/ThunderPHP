# HTML Block

Custom HTML/PHP. The final View compiler understands:

```text
{{ value }}
{{ user.name }}
{{ ROOT }}/home
{{ const:App\VERSION }}
{!! raw !!}
{% if value %}
{% foreach items as item %}
```

Bare all-uppercase identifiers are treated as PHP constants when defined. Use `const:` for namespaced or mixed-case constants.

## Optional wrapper

The HTML is emitted exactly as entered when both **Wrapper CSS classes** and **Wrapper inline styles** are empty. Setting either field creates one outer `div`; responsive width classes are applied only to that optional wrapper.
