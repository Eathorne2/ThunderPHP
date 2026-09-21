<?php

/**
 * This file is part of the ThunderPHP Framework.
 * 
 * @package ThunderPHP
 * @author Your Name <youremail@email.com>
 * @version 1.0.0
 * @license http://opensource.org/licenses/gpl-license.php  GNU Public License
 * 
 */

namespace Migration;

defined('ROOTPATH') or die("Direct script access denied");

/**
 * {CLASS_NAME} class
 */
class {CLASS_NAME} extends Migration
{

	public function up()
	{

		$this->addColumn('id int unsigned auto_increment');
		$this->addColumn('user_id int unsigned default 0');
		$this->addColumn('column1 varchar(255) null');
		$this->addColumn('column2 text null');
		$this->addColumn('slug varchar(100) null');
		$this->addColumn('deleted tinyint(1) unsigned default 0');
		$this->addColumn('date_created datetime default null');
		$this->addColumn('date_updated datetime default null');
		$this->addColumn('date_deleted datetime default null');

		$this->addPrimaryKey('id');
		$this->addKey('date_created');
        $this->addKey("user_id", "idx_posts_user_id");
        $this->addUniqueKey("slug", "uniq_posts_slug");
        $this->addFullTextKey('column2');

 		$this->addForeignKey(
            column: "user_id",
            refTable: "users",
            refColumn: "id",
            onDelete: "CASCADE",
            onUpdate: "CASCADE",
            name: "fk_posts_user_id"
        );

		$this->createTable('{TABLE_NAME}');

		/**
		 * to seed data:
		 * $this->addData([
		 * 	'username'=>'john',
		 * 	'email'=>'email@email.com',
		 * 	'gender'=>'male',
		 * ]);
		 * $this->insert('{TABLE_NAME}');
		 */

	}

	public function down()
	{
		$this->dropTable('{TABLE_NAME}');
	}
}