<?php

namespace Migration;

defined('ROOTPATH') or die("Direct script access denied");

class Create_thunder_admin_settings_table extends Migration
{
    public function up()
    {
        $this->addColumn('id int unsigned auto_increment');
        $this->addColumn('setting_key varchar(120) not null');
        $this->addColumn('setting_value text null');
        $this->addColumn('date_created datetime default null');
        $this->addColumn('date_updated datetime default null');

        $this->addPrimaryKey('id');
        $this->addUniqueKey('setting_key', 'uniq_thunder_admin_setting_key');
        $this->createTable('thunder_admin_settings');

        $this->addData([
            'setting_key'   => 'active_look',
            'setting_value' => 'classic-sidebar',
            'date_created'  => date('Y-m-d H:i:s'),
        ]);

        $this->addData([
            'setting_key'   => 'palette_classic-sidebar',
            'setting_value' => 'thunder-violet',
            'date_created'  => date('Y-m-d H:i:s'),
        ]);

        $this->insert('thunder_admin_settings');
    }

    public function down()
    {
        $this->dropTable('thunder_admin_settings');
    }
}
