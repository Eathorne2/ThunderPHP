---
title: "Checksums, Pending Detection, and Status"
slug: "checksums-pending-detection-and-status"
description: "How Thunder detects pending migrations, uses checksums, and compares disk files against tracker records."
published: true
order: 16
source_id: 146
keywords: ["checksums", "pending", "detection", "status", "thunder", "detects", "migrations", "uses", "compares", "disk", "files", "against", "tracker", "records", "database", "overview", "already-ran", "checksum", "useful", "reporting", "behavior", "practical", "interpretation", "best"]
---

**Pending detection**

Thunder determines whether a migration is pending by comparing the migration files on disk against the migration tracker records.

If a migration file exists in the plugin's `migrations/` folder but no matching tracker record exists for that plugin and file name, the migration is treated as pending.

**Already-ran detection**

Before running a migration in `up` mode, Thunder checks:

```php
$tracker->hasRun($pluginId, $migrationName)
```

If the migration is already tracked, Thunder skips it.

This prevents accidental duplicate execution.

**Checksums**

When Thunder logs a migration run, it stores a SHA-256 checksum of the migration file:

```php
hash_file('sha256', $file)
```

This gives each migration record a fingerprint of the file contents at the time it ran.

**What the checksum is useful for**

In the current implementation, the checksum is stored but not actively enforced during migration or status checks. Even so, it is valuable because it lays the groundwork for future features such as:

- detecting edited migration files after execution
- warning when a previously-ran migration has changed
- auditing migration history more reliably

**Status reporting behavior**

`migrate:status` scans the migration folder, sorts all `.php` files, and compares them with tracked runs. For each file, it prints either:

```php
[RAN]
[PENDING]
```

For ran files, Thunder also prints the batch number and run timestamp.

**Practical interpretation**

If a file appears as `[PENDING]`, one of these is usually true:

- it has never been run
- its tracker row was removed during rollback
- the tracker table was reset or lost
- the plugin id or file name changed

**Best practice**

Do not edit old migration files after they have run in shared environments. Instead, create a new migration file for the next change. That keeps your migration history stable and your checksum records meaningful.
