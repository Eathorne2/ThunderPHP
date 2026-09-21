---
title: "How Thunder Tracks Migrations"
slug: "how-thunder-tracks-migrations"
description: "A beginner explanation of the migration tracker table, batches, plugin scoping, and why this system matters."
published: true
order: 12
source_id: 160
keywords: ["thunder", "tracks", "migrations", "beginner", "explanation", "migration", "tracker", "table", "batches", "plugin", "scoping", "why", "system", "matters", "command", "line", "tool", "getting", "started", "cli", "exists", "important", "batch", "numbers"]
---

**Why a tracker table exists**

Thunder stores executed migration history in a database table named `thunder_migrations`.

This table tracks values such as:

- plugin id
- migration filename
- batch
- checksum
- run timestamp

**Why this is important**

Without tracking, Thunder would not know:

- whether a migration already ran
- which batch should be rolled back
- which plugin a migration belongs to

**Plugin scoping**

A migration is identified using both:

- `plugin_id`
- `migration_name`

That means different plugins can safely have migrations with similar names, because Thunder tracks them per plugin.

**Batch numbers**

When you run migrations, Thunder assigns a batch number. Later, rollback uses that batch information to decide what to reverse.

**Checksum**

Thunder also stores a checksum of the migration file when it logs a run. This is useful metadata for auditing and future improvement, even if you are not yet using it for enforcement.

**Beginner summary**

The migration tracker is the memory of the migration system. It tells Thunder what happened before so that future commands can behave correctly.
