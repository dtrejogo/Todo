<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migration_Install_todo extends Migration {

	public function up()
	{
		$prefix = $this->db->dbprefix;

		$this->dbforge->add_field('`id` int(11) NOT NULL AUTO_INCREMENT');
			$this->dbforge->add_field("`todo_title` VARCHAR(255) NOT NULL");
			$this->dbforge->add_field("`todo_description` TEXT NOT NULL");
			$this->dbforge->add_field("`todo_date` DATETIME NOT NULL");
			$this->dbforge->add_field("`todo_priority` SMALLINT(1) NOT NULL");
			$this->dbforge->add_field("`todo_completed` SMALLINT(1) NOT NULL");
			$this->dbforge->add_field("`todo_user_id` INT(11) NOT NULL");
			$this->dbforge->add_field("`created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'");
		$this->dbforge->add_key('id', true);
		$this->dbforge->create_table('todo');

	}

	//--------------------------------------------------------------------

	public function down()
	{
		$prefix = $this->db->dbprefix;

		$this->dbforge->drop_table('todo');

	}

	//--------------------------------------------------------------------

}