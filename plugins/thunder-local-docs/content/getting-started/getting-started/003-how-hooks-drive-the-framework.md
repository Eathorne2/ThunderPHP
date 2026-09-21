---
title: "How Hooks Drive the Framework"
slug: "how-hooks-drive-the-framework"
description: "Learn the main execution flow and how plugins plug into it."
published: true
order: 3
source_id: 265
keywords: ["hooks", "drive", "framework", "learn", "main", "execution", "flow", "plugins", "plug", "getting", "started", "lifecycle", "action", "vs", "filter", "important", "naming", "rule"]
---

ThunderPHP plugins talk to the framework using hooks.

The two main registration functions are:

```php
add_action('hook_name', function($data){}, 10, '');
add_filter('hook_name', function($data){ return $data; }, 10, '');
```

**Main Lifecycle Hooks**

1. `roles`
2. `permissions`
3. `before_controller`
4. `controller`
5. `after_controller`
6. `before_view`
7. `view`
8. `after_view`

This means a ThunderPHP page request is usually assembled by many plugins contributing behavior at different stages.

**Action vs Filter**

- use an action when you want to run code
- use a filter when you want to modify and return data

**Important Naming Rule**

Standard admin hooks should use:

- `admin_main_content`
- `admin_before_links`

For other custom hooks, follow the `{plugin_id}_hook_name` convention to avoid collisions with other plugins.
