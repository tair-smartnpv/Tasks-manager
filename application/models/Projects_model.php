<?php
use Ramsey\Uuid\Uuid;
class Projects_model extends CI_Model
{
	public function __construct()
	{
		$this->load->database();
		$this->load->model('Tasks_model');
	}

	public function get_projects()
	{
		$query = $this->db->get('projects');

		return $query->result();
	}

	public function get_projects_by_user($user_id)
	{

//		$this->db->select('name, description,created_at,uuid');
		$this->db->select('*');
		$this->db->from('projects');
		$this->db->join('users_projects', 'projects.id = users_projects.project_id');
		$this->db->where(array('users_projects.user_id'=> $user_id, 'projects.is_deleted' => 0));
		$query = $this->db->get();
		return $query->result();

	}
	public function get_projects_by_user_api($user_id)
	{

//		$this->db->select('name, description,created_at,uuid');
		$this->db->select('name,description,uuid');
		$this->db->from('projects');
		$this->db->join('users_projects', 'projects.id = users_projects.project_id');
		$this->db->where(array('users_projects.user_id'=> $user_id, 'projects.is_deleted' => 0));
		$query = $this->db->get();
		return $query->result();

	}
	public function get_project($project_id, $user_id)
	{
//		log_message('DEBUG','uuid and user id="'.$uuid.$user_id.'"');
		$this->db->select('projects.name,projects.uuid');
		$this->db->from('projects');
		$this->db->join('users_projects', 'projects.id = users_projects.project_id');
		$this->db->where('projects.id', $project_id);
		$this->db->where('users_projects.user_id', $user_id);
		$this->db->where('projects.is_deleted', 0);
		$query = $this->db->get();

		return $query->row();
	}
	public function get_api_project($project_id, $user_id){
		$this->db->select('projects.name,projects.description,projects.created_at,projects.uuid');
		$this->db->from('projects');
		$this->db->join('users_projects', 'projects.id = users_projects.project_id');
		$this->db->where('projects.id', $project_id);
		$this->db->where('users_projects.user_id', $user_id);

		$query = $this->db->get();
		return $query->row();
	}

	public function add_project($name, $description, $created_at, $user_id)
	{
		$uuid = Uuid::uuid4()->toString();
		log_message('debug', 'Adding project ' . $name . ' to ' . $uuid);

		$data =
			array('name' => $name,
				'description' => $description,
				'created_at' => $created_at,
				'user_id' => $user_id,
				'uuid' => $uuid);


		$this->db->insert('projects', $data);
		$project_id = $this->db->insert_id();
		$relation =array('user_id' => $user_id, 'project_id' => $project_id);
		$this->db->insert('users_projects', $relation);
		return $uuid;
	}

	public function get_project_id($uuid){
		return $this->db->select('projects.id')->from('projects')->where('uuid', $uuid)->get()->row()->id;
	}

	public function delete_project($id)
	{
		$tasks= $this->Tasks_model->get_tasks_by_project($id);
		foreach ($tasks as $task) {
			$this->Tasks_model->delete_task($task->id);
		}
		$this->db->where(array('id' => $id))->update('projects',array('is_deleted' => 1));
//		$this->db->where(array('project_id' => $id))->delete('users_projects');


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

//	public  function belongs_to_user($uuid, $user_id):bool
//	{
//		$project_id = $this->get_project_id($uuid);
//		if($this->db->where(array('user_id'=> $user_id,'project_id'=>$project_id))->count_all_results('users_projects') == 0){return false;}
//		return true;
//	}


	public function generate_uuid(){

	}

}

