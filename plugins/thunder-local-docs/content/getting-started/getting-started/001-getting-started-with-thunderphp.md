---
title: "Getting Started with ThunderPHP"
slug: "getting-started-with-thunderphp"
description: "An introduction to how ThunderPHP works and how to approach it as a plugin-based framework."
published: true
order: 1
source_id: 263
keywords: ["getting", "started", "thunderphp", "introduction", "works", "approach", "plugin-based", "framework", "think", "about", "main", "idea"]
---

ThunderPHP is a plugin-based framework built to combine the re-usability of plugins with the freedom of a framework.

In a normal MVC framework, features are often spread across many folders and tightly connected to the rest of the app. In ThunderPHP, a feature is usually built as a self-contained plugin that can be reused in many projects.

That means a plugin can:

- define its own routes
- define its own tables
- define its own permissions
- provide admin pages
- provide frontend pages
- be turned on or off more easily than tightly coupled app code

**How To Think About ThunderPHP**

ThunderPHP is not strict MVC and it is not a CMS.

It is best understood as a hook-driven modular framework where plugins are the main reusable unit. Controllers are usually plain execution blocks registered on hooks, though you can still use classes if you prefer.

**The Main Idea**

Build a feature once as a plugin, then reuse it in many projects with minimal changes.
