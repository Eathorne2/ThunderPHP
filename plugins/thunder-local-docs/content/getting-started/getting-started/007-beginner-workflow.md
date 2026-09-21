---
title: "Beginner Workflow"
slug: "beginner-workflow"
description: "A simple mental checklist for building a plugin from idea to working feature."
published: true
order: 7
source_id: 269
keywords: ["beginner", "workflow", "simple", "mental", "checklist", "building", "plugin", "idea", "working", "feature", "getting", "started", "best", "advice"]
---

A simple ThunderPHP workflow looks like this:

1. create the plugin with the CLI
2. define `config.json`
3. define named routes
4. set shared request values with `set_value()`
5. create migrations if the plugin needs tables
6. register `controller` hooks for logic
7. register `view` or `admin_main_content` hooks for output
8. add permissions if needed
9. validate input
10. test route flow and 404 behavior

**Best Beginner Advice**

Keep `plugin.php` focused on hook registration and small setup tasks. Move heavier logic into included files under `controllers/` and keep views inside the active look. That pattern stays readable even as the plugin grows.
