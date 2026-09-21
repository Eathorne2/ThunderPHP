---
title: "Routing & Hooks Guide Overview"
slug: "routing-hooks-guide-overview"
description: "Understand how routes and hooks combine to form ThunderPHP’s execution model."
published: true
order: 1
source_id: 270
keywords: ["routing", "hooks", "guide", "overview", "understand", "routes", "combine", "form", "thunderphp", "execution", "model", "getting", "started"]
---

ThunderPHP combines routing and hooks to build pages.

Routes decide **when** something should run.
Hooks decide **where** in the lifecycle it runs.

Unlike MVC frameworks where a route maps directly to a controller, ThunderPHP allows multiple plugins to contribute to the same request through hooks. This is what makes it powerful but also requires understanding the flow properly.

Core lifecycle:

1. before_controller
2. controller
3. after_controller
4. before_view
5. view
6. after_view

Each plugin can attach logic to any of these stages.
