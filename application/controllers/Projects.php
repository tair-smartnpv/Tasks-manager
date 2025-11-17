<?php

/**
 * @property projects_model $projects_model
 * @property input $input
 */
class projects extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('projects_model');
		$this->load->helper('url');


	}

	public function index()
	{

		$data['projects'] = $this->projects_model->get_projects();
		$this->load->view('projects_view', $data);

	}

	public function get_projects()
	{
		$projects = $this->projects_model->get_projects();

		echo json_encode($projects);
	}

	public function add()
	{
		$this->load->helper(array('form', 'url'));

		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', 'Name', 'required|min_length[3]|max_length[20]|regex_match[/^[\p{L}\p{N}\s]+$/u]',
		[
			'required' => 'יש להזין שם לפרויקט',
			'min_length'=> 'השם צריך להכיל לפחות 3 תווים',
			'max_length' => 'השם צריך להכיל עד 20 תווים',
			'regex_match' => 'השם צריך להכיל רק תווים מותרים'
		]);
		$this->form_validation->set_rules('description','Description','max_length[255]|regex_match[/^[\p{L}\p{N}\s]+$/u]',
		[
			'max_lenqth' =>'התיאור יכול להכיל עד 255 תווים.',
			'regex_match' => 'התיאור צריך להכיל רק תווים מותרים'
		]);

		if ($this->form_validation->run() == FALSE) {
			echo json_encode(['status' => 'error',
					'message' => validation_errors()]
			);
	} else {
			$name = $this->input->post('name');
			$description = $this->input->post('description');
			$created_at = time();
			$id = $this->projects_model->add_project($name, $description, $created_at);
			echo json_encode([
				'status' => 'success',

				'id' => $id,
				'name' => $name,
				'description' => $description,
				'created_at' => $created_at
			]);
		};


	}

	public function delete($id = null)
	{
		$this->projects_model->delete_project($id);

	}


}
