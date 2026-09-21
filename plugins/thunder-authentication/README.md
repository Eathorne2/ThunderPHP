# Thunder Authentication

Thunder Authentication is a companion plugin for ThunderPHP that provides login, logout, optional public registration, user profiles, account editing, user administration, roles, plugin-owned permissions, custom user fields, and configurable authentication looks.

Version: **1.1.0**

## Scope of this release

Included:

- Username, email, or combined identifier login
- Password authentication with CSRF protection
- Failed-login throttling
- Remember-me tokens
- Optional single-session enforcement
- Logout and configurable redirects
- Open, approval-required, or disabled registration
- User account and password editing
- Optional public profiles
- Profile image uploads
- Administrative user CRUD
- Account activation, disabling, suspension, and archiving
- Multiple roles per user
- Plugin-provided permission assignment
- Custom user field designer
- CSV user export
- Authentication settings
- Audit records for important account actions
- Six frontend looks, including two minimalist designs
- Preset and custom colour palettes
- Thunder Admin navigation, dashboard, notification, title, and user-menu integration
- Frontend and administration assets loaded directly by their view files
- Public extension hooks and filters

Deliberately excluded from this release:

- Email verification
- Forgot-password and password-reset workflows

Those features are intended for a separate companion plugin.


## Version 1.1.0 changes

- Controller files registered in the `plugin.php` route loop are loaded with `require_once`.
- `auth_render_view()` now loads the selected view with `require_once`.
- Password visibility buttons are removed from keyboard tab order so password confirmation is the next focus target.
- Failed form submissions restore old values across frontend, account, user, role, field, settings, and custom-field forms.
- Password values are deliberately excluded from old-input storage.
- Account and public-profile pages include Logout actions.
- Administrators receive an Admin action on account and public-profile pages.
- Four additional looks were added: Centered Minimal, Line Minimal, Dark Access, and Soft Rounded.
- Dynamic multi-select, checkbox, and toggle fields correctly preserve empty selections.

## Version 1.0.3 changes

- A completely empty database is now a supported installation starting point.
- The plugin creates its own primary user table, `auth_users`.
- Every table created by this plugin uses the `auth_` prefix to avoid collisions.
- The first successfully registered account is activated automatically and assigned the protected `admin` role.
- Frontend and administration assets continue to load directly from their view files.
- Username and email lookups require a valid numeric `auth_users.id`, preventing empty database results from being mistaken for existing accounts.

## Requirements

- ThunderPHP core compatible with `^1.0.0`
- PHP 8.0 or newer
- MySQL or MariaDB
- Thunder Admin is recommended and declared as an optional dependency

No existing application tables are required. The plugin owns its user records and supporting data.

The primary account table is:

```text
auth_users
```

It contains the core account columns `id`, `username`, `email`, `password`, `role`, `disabled`, `last_login`, `created_at`, and `updated_at`. Profile information and access-control mappings are stored in the supporting `auth_` tables.

## Installation

Copy the `thunder-authentication` folder into the ThunderPHP plugins directory, then run:

```text
php thunder migrate thunder-authentication
```

Confirm the plugin is active in `config.json`, then visit:

```text
/login
/admin/auth/users
```

The first successfully registered user becomes account ID `1`, is activated automatically, and receives the protected `admin` role. Any user whose `auth_users.role` value is `admin` is also treated as an administrator.

## Main routes

### Frontend

```text
GET  /login
POST /login
GET  /logout
GET  /signup
POST /signup
GET  /account
POST /account/profile
POST /account/password
GET  /profile/{username}
```

### Administration

```text
/admin/auth/users
/admin/auth/users/create
/admin/auth/users/{id}
/admin/auth/users/export
/admin/auth/roles
/admin/auth/roles/create
/admin/auth/roles/{id}
/admin/auth/fields
/admin/auth/fields/create
/admin/auth/fields/{id}
/admin/auth/settings
```

## Database tables

The migrations create only `auth_`-prefixed tables:

```text
auth_users
auth_user_profiles
auth_roles
auth_user_roles_map
auth_role_permissions
auth_settings
auth_user_fields
auth_user_field_options
auth_user_field_values
auth_remember_tokens
auth_login_attempts
auth_user_sessions
auth_audit_log
```


The plugin manifest loads Thunder Authentication on `all` routes. This is required so the `user_roles` and `user_permissions` filters remain available to every other plugin, not only on authentication pages.

## Registration modes

The settings page supports:

```text
Disabled
Open registration
Administrator approval required
```

Approval-required accounts are created with a `pending` profile status and cannot log in until an authorized administrator activates them.

## Roles and permissions

Permissions remain owned by the plugins that declare them. Thunder Authentication reads permissions from installed plugin `config.json` files and from the runtime `permissions` filter.

Administrators can assign those permission slugs to roles, but cannot create or rename permission definitions inside Thunder Authentication. Permission assignments belonging to an unavailable plugin are retained when a role is edited.

Effective permissions are the union of every assigned role. Account ID `1` and users with the `admin` role receive the special `all` permission.

## Custom user fields

Supported field types:

```text
Text
Email
Number
Telephone
URL
Date
Time
Textarea
Select
Multi-select
Radio buttons
Checkboxes
Toggle
Image
File
Hidden
Read only
```

Each field can control:

- Required state
- User and administrator editability
- Signup, public-profile, account, and administration placement
- Visibility level
- Allowed roles
- Validation regular expression
- Display order
- Selectable options

Selectable options use one line per option:

```text
Option label|option-value
```

## Appearance

Included looks:

- `clean-card`
- `split-screen`
- `centered-minimal`
- `line-minimal`
- `dark-access`
- `soft-rounded`

`centered-minimal` and `line-minimal` are intentionally low-chrome designs with reduced decoration and flatter surfaces.

Each look declares its own palettes in `look.json`. Administrators can select a preset, choose a custom palette, and configure form width and border radius from `/admin/auth/settings`.

Frontend and administrative views include their own look-specific CSS, palette variables, and JavaScript directly. They do not depend on foundation or Thunder Admin asset hooks.

## Failed-submit value retention

When a POST form fails, Thunder Authentication stores a one-request copy of safe input values, redirects back to the form, restores them into `$_POST`, and lets ThunderPHP's `old_value()`, `old_select()`, and `old_checked()` helpers repopulate the controls.

Keys containing `password`, `csrf`, or `token` are never stored.

## Foundation integration

Thunder Authentication renders its own content by default. A foundational plugin can wrap or replace the final authentication page through:

```php
add_action('foundation_render_auth_page', function (array $page): void {
    // $page contains title, content, look, and view.
    echo $page['content'];
});
```

A foundation can also use the `auth_render_page` filter and return a payload with `handled => true` and replacement `content`.

## Extension payload convention

ThunderPHP hooks accept one data value. Therefore, Thunder Authentication passes associative payload arrays rather than multiple positional arguments.

Example:

```php
add_action('auth_login_success', function (array $payload): void {
    $user = $payload['user'] ?? null;
});
```

## Frontend hooks

Login:

```text
auth_before_login_page
auth_before_login_form
auth_login_form_start
auth_login_fields
auth_after_login_fields
auth_login_form_end
auth_after_login_form
auth_after_login_page
```

Signup:

```text
auth_before_signup_page
auth_before_signup_form
auth_signup_form_start
auth_signup_fields
auth_after_signup_fields
auth_signup_form_end
auth_after_signup_form
auth_after_signup_page
```

Profile and account:

```text
auth_before_profile
auth_profile_header
auth_profile_sidebar
auth_profile_details
auth_profile_sections
auth_after_profile
auth_account_menu
auth_before_account_content
auth_account_tabs
auth_account_content
auth_after_account_content
auth_before_password_form
auth_password_fields
auth_after_password_form
```

## Administrative hooks

```text
auth_admin_before_users
auth_admin_user_filters
auth_admin_user_columns
auth_admin_user_row_actions
auth_admin_after_users
auth_admin_before_user
auth_admin_user_tabs
auth_admin_user_fields
auth_admin_user_sidebar
auth_admin_user_actions
auth_admin_after_user
auth_admin_before_roles
auth_admin_role_fields
auth_admin_permission_groups
auth_admin_after_roles
auth_admin_settings_tabs
auth_admin_settings_fields
auth_admin_after_settings
```

## Lifecycle actions

Important actions include:

```text
auth_before_login_attempt
auth_login_failed
auth_login_success
auth_before_logout
auth_after_logout
auth_before_registration
auth_user_registered
auth_registration_pending
auth_registration_approved
auth_before_user_create
auth_user_created
auth_before_user_update
auth_user_updated
auth_before_user_delete
auth_user_deleted
auth_user_disabled
auth_user_enabled
auth_role_created
auth_role_updated
auth_before_role_delete
auth_role_deleted
auth_role_assigned
auth_role_removed
auth_before_profile_update
auth_profile_updated
auth_profile_image_updated
auth_password_changed
```

## Important filters

```text
auth_render_page
auth_login_identifier
auth_login_credentials
auth_login_error
auth_can_login
auth_login_redirect
auth_logout_redirect
auth_registration_enabled
auth_registration_data
auth_registration_validation_rules
auth_default_role
auth_registration_redirect
auth_registration_requires_approval
auth_user_data
auth_user_display_name
auth_user_profile_url
auth_user_avatar
auth_can_edit_user
auth_can_delete_user
auth_available_roles
auth_available_permissions
auth_user_roles
auth_user_permissions
auth_can_assign_role
auth_user_fields
auth_user_field_value
auth_user_field_validation
auth_user_field_visibility
```

## Security notes

- Passwords use `password_hash()` and `password_verify()`.
- Successful login regenerates the PHP session ID.
- Forms use ThunderPHP CSRF tokens.
- Remember-me cookies are HTTP-only, SameSite Lax, and store split selector/validator tokens.
- Only validator hashes are stored in the database.
- Login throttling checks both the normalized identifier and IP address.
- Disabling a user removes remembered-login tokens and registered sessions.
- Single-session mode invalidates the previous registered session after a new login.
- Account ID `1` cannot be disabled or deleted.
- Open redirects are rejected unless the absolute URL belongs to the current host.

## Validation performed

The distributed package has been checked with:

```text
php -l
```

for every PHP file, and all `config.json` and `look.json` manifests have been parsed as valid JSON.

Runtime behavior should still be tested inside the target ThunderPHP project because the exact `users` schema, session implementation, upload paths, and active foundational plugin are application-specific.
