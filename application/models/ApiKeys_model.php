<?php


class ApiKeys_model extends CI_Model
{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function get_user($apikey){
		$query= $this->db->get_where('keys', array('key' => $apikey))->row();
		log_message('debug', 'get_user result: ' . json_encode($query));
		return $query;
	}

	public function delete_user($user_id)
	{
		$this->db->where('user_id', $user_id)->update('keys',array('is_deleted' => 1));

	}
	public function add_users($apikey){

	}
}
