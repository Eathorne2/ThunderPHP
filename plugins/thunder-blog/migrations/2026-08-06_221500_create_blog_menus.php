<?php

namespace Migration;

defined('ROOTPATH') or die('Direct script access denied');

use Core\Database;

class Create_blog_menus extends Migration
{
    public function up()
    {
        $db = new Database();
        $tables = $db->query('SHOW TABLES');
        $names = [];
        if (is_array($tables)) {
            foreach ($tables as $row) {
                foreach ((array) $row as $value) {
                    $names[] = (string) $value;
                    break;
                }
            }
        }

        if (!in_array('thunder_blog_menus', $names, true)) {
            $db->query("CREATE TABLE thunder_blog_menus (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                name VARCHAR(150) NOT NULL,
                slug VARCHAR(170) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uniq_thunder_blog_menus_slug (slug),
                KEY idx_thunder_blog_menus_name (name)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }

        if (!in_array('thunder_blog_menu_items', $names, true)) {
            $db->query("CREATE TABLE thunder_blog_menu_items (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                menu_id INT UNSIGNED NOT NULL,
                parent_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
                item_type VARCHAR(20) NOT NULL DEFAULT 'custom',
                object_id BIGINT UNSIGNED NULL,
                title VARCHAR(255) NOT NULL,
                url VARCHAR(500) NOT NULL DEFAULT '#',
                target VARCHAR(20) NOT NULL DEFAULT '_self',
                sort_order INT NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_thunder_blog_menu_items_menu (menu_id, sort_order),
                KEY idx_thunder_blog_menu_items_parent (parent_id),
                KEY idx_thunder_blog_menu_items_object (item_type, object_id),
                CONSTRAINT fk_thunder_blog_menu_items_menu FOREIGN KEY (menu_id) REFERENCES thunder_blog_menus(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }
    }

    public function down()
    {
        $db = new Database();
        $db->query('DROP TABLE IF EXISTS thunder_blog_menu_items');
        $db->query('DROP TABLE IF EXISTS thunder_blog_menus');
    }
}
