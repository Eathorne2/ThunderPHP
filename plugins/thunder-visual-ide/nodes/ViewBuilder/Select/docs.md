# Select

Enter one option per line as `value|Label`. The label may be omitted.

## Styling

**Wrapper CSS classes** are added only to the responsive `.thv-field` container. **Control CSS classes** are added only to the actual form control.

The generated base selectors intentionally use `:where()` so normal custom selectors can override them without `!important`:

```css
.my-field input {
    background: #666;
    color: #fff;
}
```

You can also override the built-in control tokens on the wrapper:

```css
.my-field {
    --thv-control-background: #666;
    --thv-control-color: #fff;
    --thv-control-border: 1px solid #444;
    --thv-control-radius: .75rem;
    --thv-control-padding: .9rem 1rem;
}
```

## Inline validation errors

Enable **Show inline error** to render validation messages beside the field only when its error value is not empty. Leave **Error variable path** blank to use `errors.<input name>` automatically, or enter an explicit path such as `errors.profile.email`. Choose **first** to display one message or **all** to render every message in the field error array.
## Inline styles

Use **Wrapper inline styles**, **Control inline styles**, or **Error inline styles** for small one-off adjustments without creating a CSS asset. Enter CSS declarations only, for example `margin-top:.5rem; background:#444; color:#fff;`.

