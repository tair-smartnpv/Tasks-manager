<?php

class Register_model extends CI_Model {
	public function __construct(){
		$this->load->database();
	}

	public function addUser($user, $password, $email){
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$data = array("username" => $user, "user_pass" => $hashed_password, "email" => $email);
		$this->db->insert("users", $data);
		return $this->db->insert_id();


	}

	public function check_email($email){
		return $this->db->get_where('users', array('email' => $email))->num_rows()>0;
	}
	public function getUser($id){
		$query = $this->db->get_where('users', array('user_id' => $id));
		return $query->row();
	}

	public function UpdateUser($id, $user, $password, $email){
		$this->db->where('user_id', $id);
		$password = password_hash($password, PASSWORD_DEFAULT);
		$this->db->update('users', array('username'=>$user,'user_pass' => $password, 'email' => $email));
	}


}
