<?php
class Validation{

		protected $CI;

		public function __construct(){
			$this->CI =& get_instance();
			$this->CI->load->database();
		}

	public function project_relate_to_user($project_id,$user_id):bool{
			$this->CI->db->select('project_id');

	}

}
