<?php

namespace Migration;

defined('ROOTPATH') or die('Direct script access denied');

class Create_auth_core_tables extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('display_name varchar(190) default null');
        $this->addColumn('bio text default null');
        $this->addColumn('image varchar(500) default null');
        $this->addColumn("status varchar(30) default 'active'");
        $this->addColumn('last_login datetime default null');
        $this->addColumn('created_at datetime default null');
        $this->addColumn('updated_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('user_id', 'uniq_auth_profile_user');
        $this->addKey('status', 'idx_auth_profile_status');
        $this->createTable('auth_user_profiles');

        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('name varchar(120) not null');
        $this->addColumn('slug varchar(120) not null');
        $this->addColumn('description text default null');
        $this->addColumn('is_protected tinyint(1) unsigned default 0');
        $this->addColumn('created_at datetime default null');
        $this->addColumn('updated_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('slug', 'uniq_auth_roles_slug');
        $this->createTable('auth_roles');
        $this->addData(['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Full application administration.', 'is_protected' => 1, 'created_at' => date('Y-m-d H:i:s')]);
        $this->addData(['name' => 'User', 'slug' => 'user', 'description' => 'Default authenticated user role.', 'is_protected' => 1, 'created_at' => date('Y-m-d H:i:s')]);
        $this->insert('auth_roles');

        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('role_id int unsigned not null');
        $this->addColumn('created_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey(['user_id', 'role_id'], 'uniq_auth_user_role');
        $this->addKey('user_id', 'idx_auth_user_roles_user');
        $this->addKey('role_id', 'idx_auth_user_roles_role');
        $this->createTable('auth_user_roles_map');

        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('role_id int unsigned not null');
        $this->addColumn('permission_slug varchar(190) not null');
        $this->addColumn('created_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey(['role_id', 'permission_slug'], 'uniq_auth_role_permission');
        $this->addKey('role_id', 'idx_auth_role_permissions_role');
        $this->createTable('auth_role_permissions');

        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('setting_key varchar(190) not null');
        $this->addColumn('setting_value longtext default null');
        $this->addColumn('updated_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('setting_key', 'uniq_auth_settings_key');
        $this->createTable('auth_settings');
    }

    public function down()
    {
        $this->dropTable('auth_settings');
        $this->dropTable('auth_role_permissions');
        $this->dropTable('auth_user_roles_map');
        $this->dropTable('auth_roles');
        $this->dropTable('auth_user_profiles');
    }
}
