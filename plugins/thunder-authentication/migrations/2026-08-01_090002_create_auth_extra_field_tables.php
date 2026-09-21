<?php

namespace Migration;

defined('ROOTPATH') or die('Direct script access denied');

class Create_auth_extra_field_tables extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('label varchar(190) not null');
        $this->addColumn('field_key varchar(190) not null');
        $this->addColumn("field_type varchar(50) default 'text'");
        $this->addColumn('description text default null');
        $this->addColumn('placeholder varchar(255) default null');
        $this->addColumn('default_value text default null');
        $this->addColumn('validation_regex varchar(500) default null');
        $this->addColumn("visibility varchar(40) default 'user_admin'");
        $this->addColumn('required tinyint(1) unsigned default 0');
        $this->addColumn('editable_user tinyint(1) unsigned default 1');
        $this->addColumn('editable_admin tinyint(1) unsigned default 1');
        $this->addColumn('show_signup tinyint(1) unsigned default 0');
        $this->addColumn('show_public tinyint(1) unsigned default 0');
        $this->addColumn('show_private tinyint(1) unsigned default 1');
        $this->addColumn('show_admin tinyint(1) unsigned default 1');
        $this->addColumn('allowed_roles_json text default null');
        $this->addColumn('sort_order int default 0');
        $this->addColumn('active tinyint(1) unsigned default 1');
        $this->addColumn('created_at datetime default null');
        $this->addColumn('updated_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('field_key', 'uniq_auth_user_fields_key');
        $this->addKey('sort_order', 'idx_auth_user_fields_order');
        $this->createTable('auth_user_fields');

        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('field_id int unsigned not null');
        $this->addColumn('option_label varchar(190) not null');
        $this->addColumn('option_value varchar(190) not null');
        $this->addColumn('sort_order int default 0');
        $this->addPrimaryKey('id');
        $this->addKey('field_id', 'idx_auth_field_options_field');
        $this->createTable('auth_user_field_options');

        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('field_id int unsigned not null');
        $this->addColumn('field_value longtext default null');
        $this->addColumn('updated_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey(['user_id', 'field_id'], 'uniq_auth_user_field_value');
        $this->addKey('field_id', 'idx_auth_field_values_field');
        $this->createTable('auth_user_field_values');
    }

    public function down()
    {
        $this->dropTable('auth_user_field_values');
        $this->dropTable('auth_user_field_options');
        $this->dropTable('auth_user_fields');
    }
}
