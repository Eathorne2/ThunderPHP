<?php

namespace Migration;

defined('ROOTPATH') or die('Direct script access denied');

use Core\Database;

class Add_pages_designs_and_featured extends Migration
{
    public function up()
    {
        $db = new Database();
        $columns = $db->query("SHOW COLUMNS FROM thunder_blog_posts");
        $names = is_array($columns) ? array_map(static fn($row) => (string)($row->Field ?? ''), $columns) : [];

        if (!in_array('post_type', $names, true)) {
            $db->query("ALTER TABLE thunder_blog_posts ADD COLUMN post_type VARCHAR(20) NOT NULL DEFAULT 'post' AFTER status");
            $db->query("ALTER TABLE thunder_blog_posts ADD INDEX idx_thunder_blog_posts_type (post_type)");
        }
        if (!in_array('is_featured', $names, true)) {
            $db->query("ALTER TABLE thunder_blog_posts ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0 AFTER post_type");
            $db->query("ALTER TABLE thunder_blog_posts ADD INDEX idx_thunder_blog_posts_featured (is_featured)");
        }

        $indexes = $db->query("SHOW INDEX FROM thunder_blog_posts");
        $indexNames = is_array($indexes) ? array_values(array_unique(array_map(static fn($row) => (string)($row->Key_name ?? ''), $indexes))) : [];
        if (in_array('uniq_thunder_blog_posts_slug', $indexNames, true)) {
            $db->query("ALTER TABLE thunder_blog_posts DROP INDEX uniq_thunder_blog_posts_slug");
        }
        if (!in_array('uniq_thunder_blog_posts_type_slug', $indexNames, true)) {
            $db->query("ALTER TABLE thunder_blog_posts ADD UNIQUE INDEX uniq_thunder_blog_posts_type_slug (post_type, slug)");
        }

        foreach (['active_header_id' => '0', 'active_footer_id' => '0'] as $key => $value) {
            $exists = $db->get_row("SELECT id FROM thunder_blog_settings WHERE setting_key = :key LIMIT 1", ['key' => $key]);
            if (!$exists) {
                $db->query("INSERT INTO thunder_blog_settings (setting_key, setting_value, created_at, updated_at) VALUES (:key, :value, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)", ['key' => $key, 'value' => $value]);
            }
        }
    }

    public function down()
    {
        $db = new Database();
        $db->query("DELETE FROM thunder_blog_settings WHERE setting_key IN ('active_header_id', 'active_footer_id')");
        // Publishing columns and indexes are retained to avoid destructive content loss.
    }
}
