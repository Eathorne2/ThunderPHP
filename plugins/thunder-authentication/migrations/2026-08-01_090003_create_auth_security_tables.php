<?php

namespace Migration;

defined('ROOTPATH') or die('Direct script access denied');

class Create_auth_security_tables extends Migration
{
    public function up()
    {
        $this->addColumn('id bigint unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('selector varchar(100) not null');
        $this->addColumn('validator_hash varchar(64) not null');
        $this->addColumn('expires_at datetime not null');
        $this->addColumn('created_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('selector', 'uniq_auth_remember_selector');
        $this->addKey('user_id', 'idx_auth_remember_user');
        $this->createTable('auth_remember_tokens');

        $this->addColumn('id bigint unsigned auto_increment');
        $this->addColumn('identifier varchar(190) default null');
        $this->addColumn('ip_address varchar(45) default null');
        $this->addColumn('user_agent varchar(500) default null');
        $this->addColumn('success tinyint(1) unsigned default 0');
        $this->addColumn('reason varchar(190) default null');
        $this->addColumn('attempted_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addKey('identifier', 'idx_auth_attempt_identifier');
        $this->addKey('ip_address', 'idx_auth_attempt_ip');
        $this->addKey('attempted_at', 'idx_auth_attempt_date');
        $this->createTable('auth_login_attempts');

        $this->addColumn('id bigint unsigned auto_increment');
        $this->addColumn('user_id int unsigned not null');
        $this->addColumn('session_token varchar(64) not null');
        $this->addColumn('ip_address varchar(45) default null');
        $this->addColumn('user_agent varchar(500) default null');
        $this->addColumn('last_seen datetime default null');
        $this->addColumn('created_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addUniqueKey('session_token', 'uniq_auth_session_token');
        $this->addKey('user_id', 'idx_auth_sessions_user');
        $this->createTable('auth_user_sessions');

        $this->addColumn('id bigint unsigned auto_increment');
        $this->addColumn('user_id int unsigned default 0');
        $this->addColumn('event_key varchar(190) not null');
        $this->addColumn('description text default null');
        $this->addColumn('context_json longtext default null');
        $this->addColumn('ip_address varchar(45) default null');
        $this->addColumn('created_at datetime default null');
        $this->addPrimaryKey('id');
        $this->addKey('user_id', 'idx_auth_audit_user');
        $this->addKey('event_key', 'idx_auth_audit_event');
        $this->createTable('auth_audit_log');
    }

    public function down()
    {
        $this->dropTable('auth_audit_log');
        $this->dropTable('auth_user_sessions');
        $this->dropTable('auth_login_attempts');
        $this->dropTable('auth_remember_tokens');
    }
}
