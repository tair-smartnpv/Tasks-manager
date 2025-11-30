<?php

defined('BASEPATH') or exit('No direct script access allowed.');


require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class APITasks extends RestController
{

	function __construct()
	{
		parent::__construct();
		$this->load->model('Tasks_model');
		$this->load->model('Projects_model');
		$this->load->model('ApiKeys_model');
		$this->load->library('form_validation');
	}

//	public function get_all_tasks_get()
//	{
//
//		$api_key = $this->input->get_request_header('X-API-KEY');
//		$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;
////		$p_id= $this->Tasks_model->get_project_id();
//
//		$result = $this->Tasks_model->get_tasks();
//		if ($result) {
//			$this->response(array('status' => 'success', 'result' => $result), RestController::HTTP_OK);
//		} else {
//			$this->response(array('status' => 'error', 'error' => 'No tasks found.'), RestController::HTTP_NOT_FOUND);
//		}
//
//	}

	public function get_task_get($id = null)
	{
		if ($id === null) {
			$this->response(array('code' => 400, 'status' => 'error', 'message' => 'Please provide ID.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$api_key = $this->input->get_request_header('X-API-KEY');
			$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;
			$p_id = $this->Tasks_model->get_project_id($id);
			$project = $this->Projects_model->get_project($p_id, $user_id);
			if (!$project) {
				$this->response(array('code' => 404, 'status' => 'error', 'message' => 'Task not found.'), RestController::HTTP_NOT_FOUND);
			}
			$task = $this->Tasks_model->get_task($id);
			if ($task) {
				$this->response(array('code' => 200, 'status' => 'success', 'message' => 'Get task successfully.', 'data' => array('task' => $task, 'project' => $project->uuid)), RestController::HTTP_OK);
			} else {
				$this->response(array('status' => 'error', 'message' => 'Task not found'), RestController::HTTP_NOT_FOUND);
			}
		}
	}

	public function get_tasks_by_project_get($uuid = null)
	{

		if ($uuid === null) {
			$this->response(array('code' => 400, 'status' => 'error', 'message' => 'Please provide project id.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$api_key = $this->input->get_request_header('X-API-KEY');
			$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;
			$p_id = $this->Projects_model->get_project_id($uuid);
			$project = $this->Projects_model->get_project($p_id, $user_id);
			if (!$project) {
				$this->response(array('code' => 404, 'status' => 'error', 'message' => 'Project not found.'), RestController::HTTP_NOT_FOUND);
			} else {
				$tasks = $this->Tasks_model->get_tasks_api($p_id);
				if ($tasks) {
					$this->response(array('code' => 200, 'status' => 'success', 'message' => 'Get tasks successfully.', 'tasks' => $tasks), RestController::HTTP_OK);
				} else {
					$this->response(array('status' => 'error', 'message' => 'No tasks were found.'), RestController::HTTP_NOT_FOUND);

				}
			}
		}
	}

//	public function get_tasks_by_order_get($id = null)
//	{
//		if ($id === null) {
//			$this->response(array('status' => 'error', 'message' => 'Please provide project id.'), RestController::HTTP_BAD_REQUEST);
//		} else {
//			$project = $this->Projects_model->get_project($id);
//			if (!$project) {
//				$this->response(array('status' => 'error', 'message' => 'Project not found.'), RestController::HTTP_NOT_FOUND);
//			} else {
//				$tasks = $this->Tasks_model->get_tasks_by_order($id);
//				if ($tasks) {
//					$this->response(array('status' => 'success', 'tasks' => $tasks), RestController::HTTP_OK);
//				} else {
//					$this->response(array('status' => 'error', 'message' => 'No tasks found.'), RestController::HTTP_NOT_FOUND);
//				}
//			}
//		}
//	}
//
//	public function get_pending_tasks_get($id = null)
//	{
//		if ($id === null) {
//			$this->response(array('status' => 'error', 'message' => 'Please provide project id.'), RestController::HTTP_BAD_REQUEST);
//		} else {
//			$project = $this->Projects_model->get_project($id);
//			if (!$project) {
//				$this->response(array('status' => 'error', 'message' => 'Project not found.'), RestController::HTTP_NOT_FOUND);
//			} else {
//				$tasks = $this->Tasks_model->get_pending_tasks($id);
//				if ($tasks) {
//					$this->response(array('status' => 'success', 'tasks' => $tasks), RestController::HTTP_OK);
//				} else {
//					$this->response(array('status' => 'error', 'message' => 'No tasks found.'), RestController::HTTP_NOT_FOUND);
//				}
//			}
//		}
//	}

	public function post_task_post($uuid = null)
	{
		if ($uuid === null) {
			$this->response(array('code' => 400, 'status' => 'error', 'message' => 'Please provide project id.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$api_key = $this->input->get_request_header('X-API-KEY');
			$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;
			$p_id = $this->Projects_model->get_project_id($uuid);
			$project = $this->Projects_model->get_project($p_id, $user_id);

			if (!$project) {
				$this->response(array('code' => 404, 'status' => 'error', 'message' => 'Project not found.'), RestController::HTTP_NOT_FOUND);
			} else {
				$title = $this->post('title');
				$deadline = $this->post('deadline');
				if ($title === null || $deadline === null) {
					$this->response(array('code' => 400, 'status' => 'error', 'message' => 'missing title or deadline.'), RestController::HTTP_BAD_REQUEST);
				} else {
					$data = array('title' => $title, 'date' => $deadline);
					$this->form_validation->set_data($data);
					$this->form_validation->set_rules('title', 'Title', 'required|regex_match[/^[\p{L}\p{N}\s]+$/u]',
						array(
							'required' => 'יש להזין שם למשימה',
							'regex_match' => 'תווים לא תקינים'
						));
					$this->form_validation->set_rules('date', 'Date', 'required', array(
						'required' => 'יש לבחור תאריך יעד'
					));
					if ($this->form_validation->run() == FALSE) {
						$this->response(array('code' => 422, 'status' => 'error', 'error' => validation_errors()), 422);
					} else {
						$timestamp = strtotime($deadline);
						$created = time();
						$id = $this->Tasks_model->add_task($title, $p_id, $created, $timestamp);
						$this->response(array('code' => 200, 'status' => 'success', 'message' => 'Task created.', 'data' => array('title' => $title, 'deadline' => $deadline)), RestController::HTTP_CREATED);
					}

				}
			}
		}
	}

	public function patch_task_patch($id = null)
	{
		if ($id === null) {
			$this->response(array('code'=>400,'status' => 'error', 'error' => 'Please provide task id.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$api_key = $this->input->get_request_header('X-API-KEY');
			$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;
			$p_id = $this->Tasks_model->get_project_id($id);
			$project = $this->Projects_model->get_project($p_id, $user_id);
			if (!$project) {
				$this->response(array('code' => 404, 'status' => 'error', 'message' => 'Task not found.'), RestController::HTTP_NOT_FOUND);
			}

			$title = $this->patch('title');
			$deadline = $this->patch('deadline');
			$status = $this->patch('status');

			$task = $this->Tasks_model->get_task($id);
			if (!$task) {
				$this->response(array('code'=>404,'status' => 'error', 'error' => 'Task not found.'), RestController::HTTP_NOT_FOUND);
			} else {
				if ($title === null || $deadline === null) {
					$this->response(array('code'=>400,'status' => 'error', 'error' => 'missing parameters.'), RestController::HTTP_BAD_REQUEST);
				} else {
					$data = array('title' => $title, 'deadline' => $deadline, 'status' => $status);
					$this->form_validation->set_data($data);
					$this->form_validation->set_rules('title', 'Title', 'required|regex_match[/^[\p{L}\p{N}\s]+$/u]',
						array(
							'required' => 'יש להזין שם למשימה',
							'regex_match' => 'תווים לא תקינים'
						));
					$this->form_validation->set_rules('deadline', 'Deadline', 'required', array(
						'required' => 'יש לבחור תאריך יעד'
					));

					if ($this->form_validation->run() == FALSE) {
						$this->response(array('code'=>422,'status' => 'error', 'error' => validation_errors()), 422);
					} else {
						$timestamp = strtotime($deadline);
						$updated = time();
						$this->Tasks_model->update_task_in_db($id, $title, $timestamp);
						$this->Tasks_model->update_task_status($id, $status);
						$this->response(array('code'=>201,'status' => 'success', 'message' => 'task updated successfully.', 'data'=>array('title' => $title,'deadline'=>$deadline,'status'=>$status, 'updated_at' => $updated)), RestController::HTTP_OK);
					}

				}
			}
		}
	}

	public function delete_task_delete($id = null)
	{
		if ($id == null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide an ID'), RestController::HTTP_BAD_REQUEST);
		} else {
			$api_key = $this->input->get_request_header('X-API-KEY');
			$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;
			$p_id = $this->Tasks_model->get_project_id($id);
			$project = $this->Projects_model->get_project($p_id, $user_id);
			if (!$project) {
				$this->response(array('code' => 404, 'status' => 'error', 'message' => 'Task not found.'), RestController::HTTP_NOT_FOUND);
			}
			$task = $this->Tasks_model->get_task($id);
			if (!$task) {
				$this->response(array('code'=>404,'status' => 'error', 'error' => 'Task not found'), RestController::HTTP_NOT_FOUND);
			} else {
				$this->Tasks_model->delete_task($id);
				$this->response(array('code'=>200,'status' => 'success', 'message' => 'Task deleted successfully.'), RestController::HTTP_OK);
			}
		}
	}


}
