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

	public function get_task($id)
	{
		return $this->db->get_where('tasks', array('id' => $id))->row();
	}

	public function add_task($title, $project_id, $created_at, $deadline)
	{
		$data = array('title' => $title,
//			'status' => $task_status,
			'project_id' => $project_id,
			'created_at' => $created_at,
			'deadline' => $deadline);


		$this->db->insert('tasks', $data);
		log_message('info', 'task added');

		return $this->db->insert_id();
	}

	public function delete_task($id)
	{
		$this->db->where( array('id' => $id))->update('tasks',array('is_deleted' => 1));
		return true;
	}


	public function get_tasks_by_project($p_id)
	{
		$query = $this->db->get_where('tasks', array('project_id=' => $p_id, 'is_deleted' => 0));

		return $query->result();
	}

	public function update_task_status($id, $status)
	{
		$this->db->where('id', $id);
		$this->db->update('tasks', array('status' => $status));
		return true;
	}

	public function update_task_in_db($id, $title, $deadline)
	{
		if ($this->db->get_where('tasks', array('id' => $id))->num_rows() == 0) {
			return false;
		} else {
			log_message('debug', 'TASK_UPDATE_START: ID=' . $id . ', Title=' . $title . ', Deadline=' . $deadline);
			$this->db->where('id', $id);
			$this->db->update('tasks', array('title' => $title, 'deadline' => $deadline));
			$query = $this->db->last_query();
			log_message('debug', 'TASK_UPDATE_SQL: ' . $query);

			return true;
		}

	}

	public function delete_tasks_by_project($p_id)
	{
		$this->db->where( array('project_id' => $p_id))->update( 'tasks', array('is_deleted' => 1));
		return true;
	}

	public function get_tasks_by_order($p_id)
	{
		$this->db->order_by('deadline', 'ASC');
		return $this->db->get_where('tasks', array('project_id' => $p_id))->result();
	}


	public function get_pending_tasks($p_id)
	{
		return $this->db->get_where('tasks', array('project_id' => $p_id, 'status' => 'pending'))->result();
	}

//public function get_project_name($p_id){
//		$query = $this ->db->
//}
}
