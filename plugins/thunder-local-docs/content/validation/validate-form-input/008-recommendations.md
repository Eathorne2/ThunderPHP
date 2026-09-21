---
title: "Recommendations"
slug: "recommendations"
description: "Best practices for writing validation rules that stay readable, reusable, and easy to maintain."
published: true
order: 8
source_id: 10
keywords: ["recommendations", "best", "practices", "writing", "validation", "rules", "stay", "readable", "reusable", "easy", "maintain", "form", "validate", "user", "input", "recommended", "validator"]
---

**Recommended practices when using the validator:**

- use `name` for user-facing forms
- use `where` for soft-delete-aware uniqueness
- use `whereNot` for inequality-based uniqueness filters
- use custom rules for domain-specific validation
- prefer hook-based extension over editing the validator
- keep custom rule names short and descriptive

A good rule set should be easy to read, easy to extend, and easy to reuse across forms.
