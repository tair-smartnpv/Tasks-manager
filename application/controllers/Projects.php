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
		$this->load->model('projects_model');
		$this->load->helper('url');
		$this->load->model('Tasks_model');


	}

	public function index()
	{
		$user_id = $_SESSION['user_id'];
		$data['projects'] = $this->projects_model->get_projects_by_user($user_id);
		$data['user_id'] = $user_id;
		$this->load->view('projects_view', $data);

	}

	public function get_projects_by_user()
	{
		$projects = $this->projects_model->get_projects_by_user($_SESSION['user_id']);

		echo json_encode($projects);
	}

	public function add()
	{
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');
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
			$id = $this->projects_model->add_project($name, $description, $created_at,$user_id);
			echo json_encode(
				array('status' => 'success',

					'id' => $id,
					'name' => $name,
					'description' => $description,
					'created_at' => $created_at,
					'user_id' => $user_id
				)

			);
		};

	}

	public function add_by(){
		var_dump($_POST);
		return;

	}

	public function delete()
	{
		$id = $this->input->post('id');
		$this->projects_model->delete_project($id);
		$this->Tasks_model->delete_tasks_by_project($id);
		echo json_encode(array("status" => "success"));


	}


}
