<?php

namespace Migration;

defined('ROOTPATH') or die('Direct script access denied');

class Create_thunder_blog_tables extends Migration
{
    public function up()
    {
        $this->addColumn('id INT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addColumn('name VARCHAR(120) NOT NULL');
        $this->addColumn('slug VARCHAR(150) NOT NULL');
        $this->addColumn('description TEXT NULL');
        $this->addColumn('created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addColumn('updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('slug', 'uniq_thunder_blog_categories_slug');
        $this->addKey('name', 'idx_thunder_blog_categories_name');
        $this->addEngine('InnoDB');
        $this->addCharset('utf8mb4');
        $this->addCollate('utf8mb4_unicode_ci');
        $this->createTable('thunder_blog_categories');

        $this->addColumn('id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addColumn("author_id VARCHAR(191) NOT NULL DEFAULT ''");
        $this->addColumn('category_id INT UNSIGNED NULL');
        $this->addColumn('title VARCHAR(255) NOT NULL');
        $this->addColumn('slug VARCHAR(191) NOT NULL');
        $this->addColumn('excerpt TEXT NULL');
        $this->addColumn('featured_image VARCHAR(500) NULL');
        $this->addColumn("status VARCHAR(30) NOT NULL DEFAULT 'draft'");
        $this->addColumn('blocks_json LONGTEXT NULL');
        $this->addColumn('content_html LONGTEXT NULL');
        $this->addColumn('content_css LONGTEXT NULL');
        $this->addColumn('content_js LONGTEXT NULL');
        $this->addColumn('palette_json TEXT NULL');
        $this->addColumn('seo_title VARCHAR(255) NULL');
        $this->addColumn('seo_description VARCHAR(500) NULL');
        $this->addColumn('view_count BIGINT UNSIGNED NOT NULL DEFAULT 0');
        $this->addColumn('published_at DATETIME NULL');
        $this->addColumn('created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addColumn('updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('slug', 'uniq_thunder_blog_posts_slug');
        $this->addKey('author_id', 'idx_thunder_blog_posts_author');
        $this->addKey('category_id', 'idx_thunder_blog_posts_category');
        $this->addKey(['status', 'published_at'], 'idx_thunder_blog_posts_status_published');
        $this->addForeignKey('category_id', 'thunder_blog_categories', 'id', 'SET NULL', 'CASCADE', 'fk_thunder_blog_posts_category');
        $this->addFullTextKey(['title', 'excerpt'], 'ft_thunder_blog_posts_search');
        $this->addEngine('InnoDB');
        $this->addCharset('utf8mb4');
        $this->addCollate('utf8mb4_unicode_ci');
        $this->createTable('thunder_blog_posts');

        $this->addColumn('id INT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addColumn('name VARCHAR(100) NOT NULL');
        $this->addColumn('slug VARCHAR(120) NOT NULL');
        $this->addColumn('created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('slug', 'uniq_thunder_blog_tags_slug');
        $this->addEngine('InnoDB');
        $this->addCharset('utf8mb4');
        $this->addCollate('utf8mb4_unicode_ci');
        $this->createTable('thunder_blog_tags');

        $this->addColumn('post_id BIGINT UNSIGNED NOT NULL');
        $this->addColumn('tag_id INT UNSIGNED NOT NULL');
        $this->addPrimaryKey(['post_id', 'tag_id'], 'pk_thunder_blog_post_tags');
        $this->addKey('tag_id', 'idx_thunder_blog_post_tags_tag');
        $this->addForeignKey('post_id', 'thunder_blog_posts', 'id', 'CASCADE', 'CASCADE', 'fk_thunder_blog_post_tags_post');
        $this->addForeignKey('tag_id', 'thunder_blog_tags', 'id', 'CASCADE', 'CASCADE', 'fk_thunder_blog_post_tags_tag');
        $this->addEngine('InnoDB');
        $this->addCharset('utf8mb4');
        $this->addCollate('utf8mb4_unicode_ci');
        $this->createTable('thunder_blog_post_tags');

        $this->addColumn('id INT UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->addColumn('setting_key VARCHAR(120) NOT NULL');
        $this->addColumn('setting_value LONGTEXT NULL');
        $this->addColumn('created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addColumn('updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('setting_key', 'uniq_thunder_blog_settings_key');
        $this->addEngine('InnoDB');
        $this->addCharset('utf8mb4');
        $this->addCollate('utf8mb4_unicode_ci');
        $this->createTable('thunder_blog_settings');

        $defaults = [
            'look' => 'editorial',
            'palette' => 'indigo',
            'custom_palette' => '',
            'posts_per_page' => '9',
            'show_author' => '1',
            'show_date' => '1',
            'show_category' => '1',
            'user_table' => 'auth_users',
            'user_primary_key' => 'id',
            'user_display_column' => 'username',
            'user_email_column' => 'email',
            'user_avatar_column' => '',
            'user_profile_column' => 'username',
        ];

        foreach ($defaults as $key => $value) {
            $this->addData([
                'setting_key' => $key,
                'setting_value' => $value,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->insert('thunder_blog_settings');
    }

    public function down()
    {
        $this->dropTable('thunder_blog_post_tags');
        $this->dropTable('thunder_blog_tags');
        $this->dropTable('thunder_blog_posts');
        $this->dropTable('thunder_blog_categories');
        $this->dropTable('thunder_blog_settings');
    }
}
