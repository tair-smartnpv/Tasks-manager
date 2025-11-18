<?php

class Tasks_model extends CI_Model
{
	public function __construct()
	{
		$this->load->database();

	}

	public function get_tasks()
	{
		$query = $this->db->get('tasks');
		return $query->result();

	}


	public function add_task($title, $task_status, $project_id, $created_at)
	{
		$data = [
			'title' => $title,
//			'status' => $task_status,
			'project_id' => $project_id,
			'created_at' => $created_at
		];

		$this->db->insert('tasks', $data);
		log_message('info', 'task added');

		return $this->db->insert_id();
	}

	public function delete_task($id)
	{
		$this->db->delete('tasks', array('id' => $id));
		return true;
	}


	public function get_tasks_by_project($p_id)
	{
		$query = $this->db->get_where('tasks', ['project_id=' => $p_id]);

		return $query->result();
	}

	public function update_task($id, $status)
	{
		$this->db->where('id', $id);
		$this->db->update('tasks', ['status' => $status]);
		return true;
	}

	public function delete_tasks_by_project($p_id)
	{
		$this->db->delete('tasks', array('project_id' => $p_id));
		return true;
	}

//public function get_project_name($p_id){
//		$query = $this ->db->
//}
}
