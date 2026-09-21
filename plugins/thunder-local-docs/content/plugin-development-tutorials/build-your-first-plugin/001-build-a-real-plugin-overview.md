---
title: "Build a Real Plugin: Overview"
slug: "build-a-real-plugin-overview"
description: "A step-by-step walkthrough for building a reusable ThunderPHP plugin the ThunderPHP way."
published: true
order: 1
source_id: 240
keywords: ["build", "real", "plugin", "overview", "step-by-step", "walkthrough", "building", "reusable", "thunderphp", "way", "dev", "tutorials", "first"]
---

This guide walks through building a real ThunderPHP plugin using the framework's current patterns.

The goal is not just to make a feature work, but to make it reusable across projects.

In this guide, the plugin will:

- define its own routes
- create its own database tables
- register hooks
- use controller files for cleaner logic
- render frontend and admin views
- validate forms
- handle images
- paginate results

This reflects ThunderPHP's design philosophy: build self-contained features that can be moved between projects with minimal friction.
