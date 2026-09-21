---
title: "Mental Model Summary"
slug: "mental-model-summary"
description: "How to think about routes and hooks together."
published: true
order: 9
source_id: 278
keywords: ["mental", "model", "summary", "think", "about", "routes", "hooks", "together", "getting", "started", "routing"]
---

Think of ThunderPHP like this:

- Routes = entry points
- Hooks = execution layers
- Plugins = feature providers

Flow:

1. route matches
2. controller hooks run
3. view hooks build output

Global view = layout
Route view = content

Once this clicks, plugin development becomes much easier.
