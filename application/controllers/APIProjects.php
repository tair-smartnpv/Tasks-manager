<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/RestController.php';
require APPPATH . '/libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class APIProjects extends RestController
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('projects_model');
		$this->load->model('Users_model');
		$this->load->library('form_validation');
		$this->load->model('ApiKeys_model');
	}

//	public function get_all_projects_get()
//	{
//
//		$result = $this->Projects_model->get_projects();
//		if ($result) {
//			$this->response(array('status' => 'success', 'result' => $result), RestController::HTTP_OK);
//		} else {
//			$this->response(array('status' => 'error', 'error' => 'No projects found'), RestController::HTTP_NOT_FOUND);
//		}
//
//	}

	public function get_project_get($uuid = null)
	{

		$api_key = $this->input->get_request_header('X-API-KEY');
		$user = $this->ApiKeys_model->get_user($api_key);
		$user_id = $user->user_id;

		log_message('DEBUG','uuid and user id="'.$uuid.$user_id.'"');
		if ($uuid === null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide an id'),
				RestController::HTTP_BAD_REQUEST);
		} else {
				$project_id = $this->Projects_model->get_project_id($uuid)->id;
				$project = $this->Projects_model->get_api_project($project_id, $user_id);
				if ($project) {
					$this->response(array('code'=>200,'status' => 'success', 'message' => 'Project found successfully.','data'=>$project), RestController::HTTP_OK);
				} else {
					$this->response(array('status' => 'error', 'error' => 'Project not found'), RestController::HTTP_NOT_FOUND);
				}

		}
	}

	public function get_projects_by_user_get()
	{
		$api_key = $this->input->get_request_header('X-API-KEY');
		$user = $this->ApiKeys_model->get_user($api_key);
		$user_id = $user->user_id;
		log_message('debug', 'user:'.$user_id);


		if (empty($api_key)) {
			$this->response(['status' => 'error', 'message' => 'API key missing'], 400);
			return;
		}
		if ($user_id === null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide user id'), RestController::HTTP_BAD_REQUEST);
		} else {
			$user = $this->Users_model->get_user($user_id);
			if (!$user) {
				$this->response(array('status' => 'error', 'error' => 'User not found'), RestController::HTTP_NOT_FOUND);

			} else {
				$projects = $this->Projects_model->get_projects_by_user($user_id);
				if ($projects) {
					$this->response(array('status' => 'success', 'projects' => $projects), RestController::HTTP_OK);
				} else {
					$this->response(array('status' => 'error', 'error' => 'No projects were found'), RestController::HTTP_NOT_FOUND);
				}
			}
		}

	}

	public function post_project_post()
	{
		$api_key = $this->input->get_request_header('X-API-KEY');
		$user = $this->ApiKeys_model->get_user($api_key);
		$user_id = $user->user_id;
		$name = $this->post('name');
		$description = $this->post('description');
		if ($user_id === null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide user id'), RestController::HTTP_BAD_REQUEST);
		} else {
			$user = $this->Users_model->get_user($user_id);
			if (!$user) {
				$this->response(array('status' => 'error', 'error' => 'User not found'), RestController::HTTP_NOT_FOUND);
			} else {
				if ($name === null || $description === null) {
					$this->response(array('status' => 'error', 'error' => 'Missing parameters'), RestController::HTTP_BAD_REQUEST);
				} else {
					$data = array('name' => $name, 'description' => $description);
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
					if ($this->form_validation->run() == FALSE) {
						$this->response(array('status' => 'error', 'error' => validation_errors()), 422);

					} else {
						$created_at = time();
						$id = $this->Projects_model->add_project($name, $description, $created_at, $user_id);
						$this->response(array('status' => 'success', 'message' => 'Project created', 'project id' => $id), RestController::HTTP_CREATED);
					}
				}
			}
		}

	}


	public function patch_project_patch($id = null)
	{
		$api_key = $this->input->get_request_header('X-API-KEY');
		$user = $this->ApiKeys_model->get_user($api_key);
		$user_id = $user->user_id;
		if(!$this->Projects_model->belongs_to_user($id, $user_id)) {
			$this->response(array('status'=>'error', 'error'=>'You do not own this project'), RestController::HTTP_FORBIDDEN);
			return;
		}
		$name = $this->patch('name');
		$description = $this->patch('description');
		log_message('debug', 'user:'.$user_id. 'name:'.$name. 'description:'.$description);
		if ($id === null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide project id'), RestController::HTTP_BAD_REQUEST);
		} else {
			$project = $this->Projects_model->get_project($id);
			if (!$project) {
				$this->response(array('status' => 'error', 'error' => 'Project not found'), RestController::HTTP_NOT_FOUND);
			} else {
				if ($name === null || $description === null) {
					$this->response(array('status' => 'error', 'error' => 'missing parameters'), RestController::HTTP_BAD_REQUEST);
				} else {
					$data = array('name' => $name, 'description' => $description);
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
					if ($this->form_validation->run() == FALSE) {
						$this->response(array('status' => 'error', 'error' => validation_errors()), 422);
					} else {
						$updated_at = time();
						$this->Projects_model->update_project($id, $name, $description);
						$this->response(array('status' => 'success', 'message' => 'Project updated successfully.', 'project_id' => $id, 'updated_at' => $updated_at),
							RestController::HTTP_OK);
					}
				}
			}
		}
	}

	public function delete_project_delete($id){
		if ($id == null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide an ID'), RestController::HTTP_BAD_REQUEST);
		}
		else{
			$project = $this->Projects_model->get_project($id);
			if (!$project) {
				$this->response(array('status' => 'error', 'error' => 'Project not found'), RestController::HTTP_NOT_FOUND);
			}
			else{
				$this->Projecrs_model->delete_project($id);
				$this->response(array('status' => 'success', 'message' => 'Project deleted successfully.'), RestController::HTTP_OK);
			}
		}
	}


}
