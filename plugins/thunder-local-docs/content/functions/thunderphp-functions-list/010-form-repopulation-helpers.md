---
title: "Form Repopulation Helpers"
slug: "form-repopulation-helpers"
description: "Helpers for keeping submitted values in forms after validation errors or page refreshes."
published: true
order: 10
source_id: 126
keywords: ["form", "repopulation", "helpers", "keeping", "submitted", "values", "forms", "after", "validation", "errors", "page", "refreshes", "functions", "thunderphp", "list", "covered", "old_value", "string", "key", "default", "type", "post", "old_select", "value"]
---

**Functions covered**

- `old_value()`
- `old_select()`
- `old_checked()`

These helpers read from `$_POST` by default, or from `$_GET` if requested.

**`old_value(string $key, string $default = '', string $type = 'post')`**

Returns a previous value from request data, usually for text inputs and textareas.

```php
<input type="text" name="title" value="<?= esc(old_value('title')) ?>">
<textarea name="body"><?= esc(old_value('body')) ?></textarea>
```

You can also provide a default value.

```php
<input type="text" name="status" value="<?= esc(old_value('status', 'draft')) ?>">
```

**`old_select(string $key, string $value, string $default = '', string $type = 'post')`**

Returns `selected` for a matching `select` option.

```php
<select name="status">
    <option value="draft"<?= old_select('status', 'draft', 'draft') ?>>Draft</option>
    <option value="published"<?= old_select('status', 'published') ?>>Published</option>
</select>
```

**`old_checked(string $key, string $value, string $default = '', string $type = 'post')`**

Returns `checked` for matching radio or checkbox inputs.

```php
<input type="radio" name="visibility" value="public"<?= old_checked('visibility', 'public', 'public') ?>>
<input type="radio" name="visibility" value="private"<?= old_checked('visibility', 'private') ?>>
```

**Using GET instead of POST**

```php
<input type="text" name="search" value="<?= esc(old_value('search', '', 'get')) ?>">
```

**Recipe: sticky search form**

```php
<form method="get">
    <input type="text" name="q" value="<?= esc(old_value('q', '', 'get')) ?>">
    <button>Search</button>
</form>
```
