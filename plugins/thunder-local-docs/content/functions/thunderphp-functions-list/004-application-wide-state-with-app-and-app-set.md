---
title: "Application-Wide State with APP and APP_SET"
slug: "application-wide-state-with-app-and-app-set"
description: "How to read shared application data using `APP()` and a proposed complementary setter for writing to the shared `$APP` store."
published: true
order: 4
source_id: 120
keywords: ["application-wide", "state", "app", "app_set", "read", "shared", "application", "data", "proposed", "complementary", "setter", "writing", "store", "functions", "thunderphp", "list", "covered", "string", "key", "companion", "writer", "copy-paste", "implementation", "examples"]
---

**Functions covered**

- `APP()`
- proposed `APP_SET()`

**`APP(string $key = '')`**

Reads application-level values from the shared `$APP` store. Unlike `set_value()` and `get_value()`, this data is not isolated per plugin.

```php
$all = APP();
$plugins = APP('plugins');
$url = APP('URL');
```

This is useful for framework-level shared state such as loaded plugins, URL segments, matched routes, and app context.

**When to use `APP()`**

Use `APP()` when you need shared framework state that is intentionally global across plugins.

Use `set_value()` and `get_value()` when the data is temporary and should be isolated to the current plugin.

**Proposed companion writer: `APP_SET()`**

A natural opposite of `APP()` is a helper that writes to the shared `$APP` variable. This is useful when you want a small, consistent helper instead of writing to the global directly.

**Copy-paste implementation**

```php
function APP_SET(string|array $key, mixed $value = ''): bool
{
    global $APP;

    if(is_array($key))
    {
        foreach($key as $k => $v)
        {
            $APP[$k] = $v;
        }
        return true;
    }

    $APP[$key] = $value;
    return true;
}
```

**Examples**

```php
APP_SET('route_handled', true);

APP_SET([
    'custom_debug' => true,
    'feature_x'    => 'enabled',
]);
```

**Suggested usage notes**

- Use this sparingly because it writes into global shared application state.
- Prefer plugin-isolated `set_value()` for plugin-local runtime data.
- Prefer `APP_SET()` only for true application-level flags or values that are meant to be shared across plugins.

**Optional nested-key version**

If you want dot notation like `APP_SET('config.debug', true)`, that should be implemented as a separate helper rather than changing the basic version.

**Recipe: mark a request-wide flag**

```php
APP_SET('my_custom_flag', true);

if(APP('my_custom_flag')) {
    // do something once per request
}
```
