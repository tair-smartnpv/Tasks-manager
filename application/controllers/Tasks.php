<?php
/**
 * @property Tasks_model $Tasks_model
 * @property Projects_model $Projects_model
 *
 * @property input $input
 */

class Tasks extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Tasks_model');
		$this->load->helper('url');
		$this->load->model('Projects_model');
		$this->load->helper(array('url', 'form'));
		$this->load->library('form_validation');

	}


	public function index($uuid)
	{
		$user_id = $_SESSION['user_id'];
		$project_id= $this->Projects_model->get_project_id($uuid);

		$project = $this->Projects_model->get_project($project_id,$user_id);
		log_message('DEBUG','Projects: '. json_encode($project));
		if($project == null){
			$data['heading']= 'Error';
			$data['message']= 'The page you request was not found';

			$this->load->view('errors/html/error_404',$data);
		}
		else {
			$data['project'] = $project;
			$data['project_uuid'] = $uuid;

			$this->load->view('Tasks_view', $data);
		}
	}

	public function get_by_project($project_uuid)

	{

		$project_id= $this->Projects_model->get_project_id($project_uuid);
		$tasks = $this->Tasks_model->get_tasks_by_project($project_id);
		echo json_encode($tasks);

	}

	public function add()
	{

		$this->form_validation->set_rules('title', 'Title', 'required|regex_match[/^[\p{L}\p{N}\s]+$/u]',
			array(
				'required' => 'יש להזין שם למשימה',
				'regex_match' => 'תווים לא תקינים'
			));
		$this->form_validation->set_rules('date', 'Date', 'required', array(
			'required' => 'יש לבחור תאריך יעד'
		));


		if ($this->form_validation->run() == FALSE) {
			echo json_encode(array('status' => 'error', 'message' => validation_errors()));
		} else {
			$title = $this->input->post('title');
			$created_at = time();
			$task_status = 'pending';
			$project_uuid = $this->input->post('p_uuid');
			$project_id= $this->Projects_model->get_project_id($project_uuid);
			$date = $this->input->post('date');
			$deadline = strtotime($date);
			$id = $this->Tasks_model->add_task($title, $project_id, $created_at, $deadline);

			echo json_encode(array(
				'status' => 'success',
				'title' => $title,
				'created_at' => $created_at,
				'task_status' => $task_status,
				'project_uuid' => $project_uuid,
				'id' => $id,
				'deadline' => $deadline
			));
		}

	}

	public function delete()
	{
		$id = $this->input->post('id');
		$this->Tasks_model->delete_task($id);
		$data['tasks'] = $this->Tasks_model->get_tasks();
		$this->load->view('Tasks_view', $data);

	}

	public function update_status()
	{
		$status = $this->input->post('status');
		$id = $this->input->post('task_id');
		$res = $this->Tasks_model->update_task_status($id, $status);
		if ($res) {
			echo json_encode(array('status' => 'success'));
		}

	}

	public function update_task()
	{
		$this->form_validation->set_rules('title', 'Title', 'required|regex_match[/^[\p{L}\p{N}\s]+$/u]',
			array(
				'required' => 'יש להזין שם למשימה',
				'regex_match' => 'תווים לא תקינים'
			));
		$this->form_validation->set_rules('deadline', 'Deadline', 'required', array(
			'required' => 'יש לבחור תאריך יעד'
		));



		if ($this->form_validation->run() == FALSE) {
			echo json_encode(array('status' => 'error', 'message' => validation_errors()));
		} else {
			$id = $this->input->post('id');
			$title = $this->input->post('title');
			$deadline = $this->input->post('deadline');
			$deadline = strtotime($deadline);
			if ($this->Tasks_model->update_task_in_db($id, $title, $deadline)) {
				echo json_encode(array('status' => 'success'));
			}
			else{
				echo json_encode(array('status' => 'error', 'message' => 'עדכון המשימה נכשל במודל או שלא בוצעו שינויים.','date'=>$deadline));			}
		}
	}


}
