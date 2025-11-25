<?php

class projects_model extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
	}

	public function get_projects()
	{
		$query = $this->db->get('projects');

		return $query->result();
	}

	public function get_projects_by_user($user_id)
	{
		$query = $this->db->get_where('projects', array('user_id' => $user_id));
		return $query->result();

	}

	public function get_project($id)
	{
		return $this->db->get_where('projects', array('id' => $id))->row();
	}

	public function add_project($name, $description, $created_at, $user_id)
	{
		$data =
			array('name' => $name,
				'description' => $description,
				'created_at' => $created_at,
				'user_id' => $user_id);

		$this->db->insert('projects', $data);
		return $this->db->insert_id();
	}

	public function delete_project($id)
	{
		$this->db->delete('projects', array('id' => $id));

	}

	public function count_tasks($id)
	{
		$total = $this->db->where('project_id', $id)->count_all_results('tasks');;
		log_message('debug', $total);
		$completed = $this->db->where(
			array('project_id' => $id,
				'status' => 'completed'))->count_all_results('tasks');
		log_message('debug', $completed);
		return array('total'=>$total,'completed'=> $completed);
	}

	public function update_project($id, $name, $description){
		$this->db->where('id', $id)->update('projects', array('name' => $name, 'description' => $description));
	}

}

