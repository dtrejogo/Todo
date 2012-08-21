<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Todo_model extends BF_Model {

	protected $table		= "todo";
	protected $key			= "id";
	protected $soft_deletes	= false;
	protected $date_format	= "datetime";
	protected $set_created	= true;
	protected $set_modified = false;
	protected $created_field = "created_on";
        
        
        function get_priorities(){
            return array(1=>'High', 2=>'Medium',3=>'low');
        }
}
