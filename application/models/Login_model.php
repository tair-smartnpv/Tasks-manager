<?php
class Login_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}

	public function login($email, $input_pass) {
		$this->db->select('user_pass, user_id');
		$this->db->where('email', $email);
		$query = $this->db->get('users');
		if ($query->num_rows() == 0) {
			return json_encode(array('status' => 'fail', 'message' => 'User not found'));
		}
		$pass = $query->row()->user_pass;

		if (password_verify($input_pass, $pass)) {
			$id= $query->row()->user_id;
			$this->db->where('user_id', $id);
			$this->db->update('users', array('connect' => 'connected'));
			return json_encode(array('status' => 'success','user_id' => $id));
		}
		else {
			return json_encode(array('status' => 'error','message' => 'Wrong password'));
		}
	}

	public function logout($userId) {
		$this->db->where('user_id', $userId);
		$this->db->update('users', array('connect' => 'away'));
		return true;
	}
}
