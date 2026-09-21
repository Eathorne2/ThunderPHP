---
title: "Internal Behavior Notes"
slug: "internal-behavior-notes"
description: ""
published: true
order: 18
source_id: 32
keywords: ["internal", "behavior", "notes", "query", "builder", "initialisation", "usage"]
---

- All queries use PDO prepared statements
- Bindings are auto-generated (`:param_0`, etc.)
- Builder resets after execution (`get()`, `insert()`, etc.)
- Raw queries also reset builder state
- Errors are stored internally and accessible via getters
