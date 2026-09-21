<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

auth_admin_guard('auth.view_user_fields');
$rows = auth_db()->query('SELECT f.*, COUNT(v.id) AS value_count FROM ' . auth_table('fields') . ' f
    LEFT JOIN ' . auth_table('field_values') . ' v ON v.field_id = f.id
    GROUP BY f.id ORDER BY f.sort_order ASC, f.id ASC');
set_value(['fields' => is_array($rows) ? $rows : []]);
