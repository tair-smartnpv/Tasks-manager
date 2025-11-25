<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/RestController.php';
require APPPATH . '/libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class APIProjects extends RestController {

	public function __construct(){
		parent::__construct();
		$this->load->model('projects_model');
		$this->load->library('form_validation');
	}

	public function get_all_projects_get(){

		$result = $this->projects_model->get_projects();
		if($result){
			$this->response(array('result' => $result), 200);
		}
		else{
			$this->response(array('error' => 'No results found'), 404);
		}

	}
	public function get_project_get()
	{
		$id = $this->get('id');
		if($id === null){
			$this->response(array('error' => 'Parameter missing: id'), 400);

		}
		else{
			$project =$this->projects_model->get_project($id);
			if($project){
				$this->response(array('result' => $project), 200);
			}
			else{
				$this->response(array('error' => 'No results found'), 404);
			}
		}
	}
	public function get_projects_by_user_get()
	{
		$id = $this->get('id');
		if($id === null){
			$this->response(array('status'=>'error','message'=>'missing parameter id'), 400);
		}
		else{$projects = $this->projects_model->get_projects_by_user($id);
			if($projects){
				$this->response($projects, 200);
			}
			else{
				$this->response(array('error' => 'No projects were found'), 404);
			}}

	}

	public function post_project_post(){

		$name = $this->post('name');
		$description = $this->post('description');
		$user_id = $this->post('user_id');
		if($name === null || $description === null){
			$this->response(array('error'=>'missing parameters'), 400);
		}
		else{
			$data = array('name'=>$name,'description'=>$description);
			$this->form_validation->set_data($data);
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
			if ($this->form_validation->run() == FALSE){
				$this->response(array('error'=>validation_errors()), 400);

			}
			else{
				$created_at = time();
				$id=$this->projects_model->add_project($name,$description,$created_at,$user_id);
				$this->response(array('id'=>$id), 200);
			}
		}

	}


	public function put_project_put(){
		$project_id = $this->put('project_id');
		$name = $this->put('name');
		$description = $this->put('description');
		if($name === null || $description === null){
			$this->response(array('error'=>'missing parameters'), 400);
		}
		else{
			$data = array('name'=>$name,'description'=>$description);
			$this->form_validation->set_data($data);
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
			if ($this->form_validation->run() == FALSE){
				$this->response(array('error'=>validation_errors()), 400);
			}
			else{
				$updated_at = time();
				$this->projects_model->update_project($project_id,$name,$description);
				$this->response(array('id'=>$project_id,'updated at'=>$updated_at), 200);
			}
		}
	}


}
