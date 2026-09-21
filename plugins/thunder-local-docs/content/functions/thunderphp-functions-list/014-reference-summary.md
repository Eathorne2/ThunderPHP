---
title: "Reference Summary"
slug: "reference-summary"
description: "Compact summary table of the documented helper functions and their primary purpose."
published: true
order: 14
source_id: 130
keywords: ["reference", "summary", "compact", "table", "documented", "helper", "functions", "primary", "purpose", "thunderphp", "list", "request", "url", "plugin", "context", "state", "application-wide", "routes", "hooks", "paths", "views", "plugins", "compatibility", "roles"]
---

**Request and URL**

- `current_url()` — get the full current URL
- `base_url()` — build internal URLs from `ROOT`
- `split_url()` — split a URL into segments
- `URL()` — read a URL segment or all segments
- `page()` — shortcut for `URL(0)`
- `redirect()` — redirect to an internal path

**Plugin context and state**

- `set_value()` — store plugin-isolated request data
- `get_value()` — retrieve plugin-isolated request data
- `plugin_id()` — current plugin id
- `get_context()` — current plugin context object

**Application-wide state**

- `APP()` — read shared application state
- `APP_SET()` — proposed helper to write shared application state

**Routes and hooks**

- `is_route()` — test current matched route
- `get_route_name()` — current matched route name
- `get_param()` — named route parameter
- `add_action()` — register action callback
- `do_action()` — execute action callbacks
- `add_filter()` — register filter callback
- `do_filter()` — execute filter callbacks

**Paths and views**

- `current_look()` — filesystem path inside current look
- `current_look_http()` — HTTP path inside current look
- `plugin_path()` — filesystem path inside current plugin
- `plugin_http_path()` — HTTP path inside current plugin
- `class_path()` — path to another plugin model class

**Plugins and compatibility**

- `plugin_exists()` — detect an active plugin by id
- `missing_plugins()` — list missing dependencies
- `outdated_plugins()` — list core-incompatible plugins
- `show_plugins()` — debug loaded plugin names

**Roles and permissions**

- `user_roles()` — current roles
- `contains_role()` — role check
- `user_can()` — permission check

**Forms and CSRF**

- `old_value()` — sticky value for inputs
- `old_select()` — sticky selected option
- `old_checked()` — sticky checked state
- `csrf()` — generate token or hidden field
- `csrf_verify()` — verify submitted token

**Utility helpers**

- `get_image()` — image path with placeholder fallback
- `esc()` — escape HTML
- `get_date()` — human-readable date
- `message()` — flash messaging
- `dd()` — debug dump
