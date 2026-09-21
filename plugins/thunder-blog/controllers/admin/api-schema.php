<?php

namespace ThunderBlog;

defined('ROOTPATH') or die('Direct script access denied');

blog_require_permission('manage-blog-settings');
$table = trim((string) ($_GET['table'] ?? ''));
if ($table === '') {
    blog_json_response(['ok' => true, 'data' => ['tables' => blog_database_tables(), 'columns' => []]]);
}
if (!in_array($table, blog_database_tables(), true)) {
    blog_json_response(['ok' => false, 'message' => 'The selected table does not exist.'], 404);
}
blog_json_response(['ok' => true, 'data' => ['table' => $table, 'columns' => blog_table_columns($table)]]);
