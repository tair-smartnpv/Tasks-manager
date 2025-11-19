<?php
class Login_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	public function login($email) {


		$this->db->select('user_id, username');
		$this->db->where('email', $email);

		$query = $this->db->get('users');
		$id= $query->row()->user_id;
		$name= $query->row()->username;
		$this->db->where('user_id', $id);
		$this->db->update('users', array('connect' => 'connected'));

		return array('user_id' => $id, 'name' => $name);
	}

	public function logout($userId) {
		$this->db->where('user_id', $userId);
		$this->db->update('users', array('connect' => 'away'));
		return true;
	}

	public function user_exists($email) {
		return $this->db->get_where('users', array('email' => $email))->num_rows()==1;
	}

	public function correct_password($email, $password)
	{
		$hashes_pass = $this->db->get_where('users', array('email' => $email))->row()->user_pass;
		if (password_verify($password, $hashes_pass)) {
			return true;
		}
		else {
			return false;
		}

	}

}
