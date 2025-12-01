<?php

class Users_model extends CI_Model {
	public function __construct(){
		$this->load->database();
		$this->load->model('Projects_model');
		$this->load->model('ApiKeys_model');
	}

	public function add_user($user, $password, $email){
		$hashed_password = password_hash($password, PASSWORD_DEFAULT);
		$data = array("username" => $user, "user_pass" => $hashed_password, "email" => $email);
		$this->db->insert("users", $data);
		return $this->db->insert_id();


	}

	public function email_exist($email){
		return $this->db->get_where('users', array('email' => $email))->num_rows()>0;
	}
	public function get_user($id){
		$query = $this->db->select('username,email')->from('users')->where (array('user_id' => $id))->get();
		return $query->row();
	}

	public function Update_user($id, $name, $password, $email){
		$this->db->where('user_id', $id);
		$password = password_hash($password, PASSWORD_DEFAULT);
		$this->db->update('users', array('username'=>$name,'user_pass' => $password, 'email' => $email));
		$user = $this->db->get_where('users', array('user_id' => $id))->result();
		return $user;
	}

	public function get_users_list(){
		$query = $this->db->select('username, email')->get('users');

//		log_message('debug', $result);
		log_message('DEBUG', 'getUsersList()');
		return $query->result();
	}

	public function delete_user($id){
		$projects = $this->Projects_model->get_projects_by_user($id);
		foreach ($projects as $project){
			$this->Projects_model->delete_project($project->id);
		}
		$this->db->where( array('user_id' => $id))->update('users',array('is_deleted' => 1));
		$this->ApiKeys_model->delete_user($id);
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
