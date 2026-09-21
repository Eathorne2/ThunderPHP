---
title: "ThunderPHP Plugin Development Overview"
slug: "thunderphp-plugin-development-overview"
description: "What ThunderPHP is, why it exists, and how plugin development differs from plain MVC or WordPress."
published: true
order: 1
source_id: 224
keywords: ["thunderphp", "plugin", "development", "overview", "why", "exists", "differs", "plain", "mvc", "wordpress", "reference", "refrence", "developers", "think", "about"]
---

ThunderPHP is a plugin-based PHP framework that sits between a traditional MVC framework and a WordPress-style modular system.

The goal is to combine two things:

- the re-usability and switchable feature model of plugins
- the control and freedom of a framework

In ThunderPHP, a feature is usually built as a self-contained plugin. That plugin can then be reused in many projects, turned on or off, migrated independently, and extended through hooks.

**Why ThunderPHP Exists**

ThunderPHP was designed to solve problems common in ordinary MVC projects:

- features are often tightly coupled to the app
- reusing a full feature in another project is harder than it should be
- turning a feature off usually means editing many files
- adding new features often means touching unrelated code

It also addresses limitations common in CMS-driven systems:

- database structure is not imposed by the core
- authentication, admin systems, and user tables can be designed by plugin authors
- developers keep low-level control instead of inheriting a fixed app structure

In short, ThunderPHP aims to deliver the power of plugins with the independence of a framework.

**How To Think About It**

ThunderPHP is **not** a strict MVC framework.

It is better described as a hook-driven modular framework with optional MVC-style organization.

That means:

- plugins are the main unit of re-use
- hooks are the main communication layer
- controllers are execution blocks, not necessarily controller classes
- the database is defined by plugins, not by the core

A developer is free to write class-based controllers and richer architecture when needed, but the framework does not force that model.
