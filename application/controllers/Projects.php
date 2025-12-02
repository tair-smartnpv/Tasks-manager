<?php

/**
 * @property projects_model $projects_model
 * @property input $input
 * @property  Tasks_model $Tasks_model;
 */
class projects extends CI_Controller
{


	public function __construct()
	{
		parent::__construct();
		$this->load->model('Projects_model');
		$this->load->helper('url');
		$this->load->model('Tasks_model');
		$this->load->model('Users_model');
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');



	}

	public function index()
	{
		$user_id = $_SESSION['user_id'];

		$data['user_id'] = $user_id;
		$data['username'] = $_SESSION['username'];
		$projects = $this->Projects_model->get_projects_by_user($user_id);
//		foreach ($projects as $project) {
//			$progress = $this->Projects_model->count_tasks($project->id);
//			$total = $progress['total'];
//			$completed = $progress['completed'];
//			$project->total_tasks = $total;
//			$project->completed_tasks = $completed;
//			log_message('DEBUG','total' . $total . ' completed' . $completed);
//		}
		$data['projects'] = $projects;

		$this->load->view('projects_view', $data);

	}

	public function get_projects_by_user()
	{
		$projects = $this->Projects_model->get_projects_by_user($_SESSION['user_id']);
		foreach ($projects as $project) {
			$progress = $this->Projects_model->count_tasks($project->uuid);
			$total = $progress['total'];
			$completed = $progress['completed'];
			$project->total_tasks = $total;
			$project->completed_tasks = $completed;
			log_message('DEBUG','total' . $total . ' completed' . $completed);
		}
		echo json_encode($projects);
	}

	public function add()
	{
		$this->form_validation->set_rules('name', 'Name', 'required|min_length[3]|max_length[20]|regex_match[/^[\p{L}\p{N}\s]+$/u]',

			array('required' => 'יש להזין שם לפרויקט',
				'min_length' => 'השם צריך להכיל לפחות 3 תווים',
				'max_length' => 'השם צריך להכיל עד 20 תווים',
				'regex_match' => 'השם צריך להכיל רק תווים מותרים')
		);
		$this->form_validation->set_rules('description', 'Description', 'max_length[255]|regex_match[/^[\p{L}\p{N}\s]+$/u]',

			array('max_length' => 'התיאור יכול להכיל עד 255 תווים.',
				'regex_match' => 'התיאור צריך להכיל רק תווים מותרים')
		);

		if ($this->form_validation->run() == FALSE) {
			echo json_encode(array('status' => 'error',
					'message' => validation_errors())
			);
		} else {
			$name = $this->input->post('name');
			$description = $this->input->post('description');
			$created_at = time();
			$user_id = $_SESSION['user_id'];
			$uuid = $this->Projects_model->add_project($name, $description, $created_at,$user_id);
			echo json_encode(
				array('status' => 'success',

					'uuid' => $uuid,
					'name' => $name,
					'description' => $description,
					'created_at' => $created_at,
					'user_id' => $user_id
				)

			);
		};

	}


	public function share_project(){
		
		$project_uuid = $this->input->post('project_id');
		$email = $this->input->post('email');
		log_message('DEBUG','project: '. $project_uuid .'email: '. $email);
		$project_id = $this->Projects_model->get_project_id($project_uuid); 
		$user = $this->Users_model->get_user_by_email($email);
		if(!$user){
			echo json_encode(array('status'=> 'error','message'=>'משתמש לא נמצא'));

		}
		else{
			if($this->Projects_model->check_if_shared($project_id, $user->user_id)){
				echo json_encode(array('status'=>'error','message'=>'הפרויקט כבר משותף עם משתמש זה'));
			}
			else{
				$this->Projects_model->share_project($project_id, $user->user_id);
				echo json_encode(array('status'=>'success','message'=>'הפרויקט שותף בהצלחה'));
			}
		}
	}
	public function delete()
	{
		$uuid = $this->input->post('uuid');
		log_message('DEBUG','delete project_uuid' . $uuid);
		$id = $this->Projects_model->get_project_id($uuid);
		log_message('DEBUG','delete project_id' . $id);
		$this->Projects_model->delete_project($id);
//		$this->Tasks_model->delete_tasks_by_project($id);
		echo json_encode(array("status" => "success"));


	}

	public function progress($project_id){
		$project = $this->Projects_model->count_tasks($project_id);
		$total = $project['total'];
		$completed = $project['completed'];
		echo json_encode(array('total'=>$total,'completed'=>$completed));
	}



}
