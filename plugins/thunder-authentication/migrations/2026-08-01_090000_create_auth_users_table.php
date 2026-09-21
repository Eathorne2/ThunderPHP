<?php

namespace Migration;

defined('ROOTPATH') or die('Direct script access denied');

class Create_auth_users_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('username varchar(80) not null');
        $this->addColumn('email varchar(190) not null');
        $this->addColumn('password varchar(255) not null');
        $this->addColumn("role varchar(120) default 'user'");
        $this->addColumn('disabled tinyint(1) unsigned default 0');
        $this->addColumn('last_login datetime default null');
        $this->addColumn('created_at datetime default null');
        $this->addColumn('updated_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('username', 'uniq_auth_users_username');
        $this->addUniqueKey('email', 'uniq_auth_users_email');
        $this->addKey('role', 'idx_auth_users_role');
        $this->addKey('disabled', 'idx_auth_users_disabled');
        $this->addKey('created_at', 'idx_auth_users_created_at');
        $this->createTable('auth_users');
    }

    public function down()
    {
        $this->dropTable('auth_users');
    }
}
