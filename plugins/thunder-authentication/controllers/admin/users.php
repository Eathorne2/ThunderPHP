<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

auth_admin_guard(is_route('auth.admin.users.export') ? 'auth.export_users' : 'auth.view_users');
$filters = [
    'q' => trim((string)($_GET['q'] ?? '')),
    'role' => trim((string)($_GET['role'] ?? '')),
    'status' => trim((string)($_GET['status'] ?? '')),
];

if (is_route('auth.admin.users.export')) {
    $result = auth_list_users($filters, 1, 10000);
    $filename = 'users-' . date('Y-m-d-His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $out = fopen('php://output', 'wb');
    fputcsv($out, ['ID', 'Username', 'Display Name', 'Email', 'Status', 'Roles', 'Created', 'Last Login']);
    foreach ($result['users'] as $user) {
        fputcsv($out, [
            $user->id, $user->username, $user->display_name, $user->email ?? '', $user->status,
            implode(', ', array_map(static fn(object $role): string => (string)$role->name, $user->roles)),
            $user->date_created ?? '', $user->last_login ?? ''
        ]);
    }
    fclose($out);
    exit;
}

$page = max(1, (int)($_GET['page'] ?? 1));
$result = auth_list_users($filters, $page, 25);
set_value([
    'users_result' => $result,
    'user_filters' => $filters,
    'available_roles' => auth_all_roles(),
]);
