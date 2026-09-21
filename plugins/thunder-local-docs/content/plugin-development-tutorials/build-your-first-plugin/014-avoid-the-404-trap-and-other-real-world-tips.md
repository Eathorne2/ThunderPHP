---
title: "Avoid the 404 Trap and Other Real-World Tips"
slug: "avoid-the-404-trap-and-other-real-world-tips"
description: "Common guide-level mistakes and how to avoid them in real plugins."
published: true
order: 14
source_id: 253
keywords: ["avoid", "trap", "other", "real-world", "tips", "common", "guide-level", "mistakes", "them", "real", "plugins", "plugin", "dev", "tutorials", "build", "first", "always", "provide", "view", "hook", "routes", "end", "non-view", "explicitly"]
---

**Always Provide a View Hook for View Routes**

If a route is meant to show HTML, make sure the relevant `view` or admin content hook actually runs. Otherwise the framework may treat the route as unresolved and trigger 404 fallback handling.

**End Non-View Routes Explicitly**

For JSON or AJAX-only routes, output the response and end the request.

**Use Included Controller Files for Large Logic**

ThunderPHP encourages hook-driven controllers, but large procedural blocks should still be split into included files.

**Prefer Current Class Behavior**

Build new plugins using the updated Request, Image, Pager, Validate, migration, and query builder docs rather than relying on older class summaries.

**Think in Reusable Features**

If a piece of code might be useful in another project, it probably belongs in the plugin instead of being spread across the app.
