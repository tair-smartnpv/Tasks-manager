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
		$this->load->library('form_validation');
	}

	public function get_all_tasks_get()
	{
		$result = $this->Tasks_model->get_tasks();
		if ($result) {
			$this->response(array('status' => 'success', 'result' => $result), RestController::HTTP_OK);
		} else {
			$this->response(array('status' => 'error', 'error' => 'No tasks found.'), RestController::HTTP_NOT_FOUND);
		}

	}

	public function get_task_get($id = null)
	{
		if ($id === null) {
			$this->response(array('status' => 'error', 'message' => 'Please provide ID.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$task = $this->Tasks_model->get_task($id);
			if ($task) {
				$this->response(array('status' => 'success', 'message' => $task), RestController::HTTP_OK);
			} else {
				$this->response(array('status' => 'error', 'message' => 'Task not found'), RestController::HTTP_NOT_FOUND);
			}
		}
	}

	public function get_tasks_by_project_get($id = null)
	{
		if ($id === null) {
			$this->response(array('status' => 'error', 'message' => 'Please provide project id.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$project = $this->Projects_model->get_project($id);
			if (!$project) {
				$this->response(array('status' => 'error', 'message' => 'Project not found.'), RestController::HTTP_NOT_FOUND);
			} else {
				$tasks = $this->Tasks_model->get_tasks_by_project($id);
				if ($tasks) {
					$this->response(array('status' => 'success', 'tasks' => $tasks), RestController::HTTP_OK);
				} else {
					$this->response(array('status' => 'error', 'message' => 'No tasks were found.'), RestController::HTTP_NOT_FOUND);

				}
			}
		}
	}

	public function post_task_post($project_id = null)
	{
		if ($project_id === null) {
			$this->response(array('status' => 'error', 'message' => 'Please provide project id.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$project = $this->Projects_model->get_project($project_id);
			if (!$project) {
				$this->response(array('status' => 'error', 'message' => 'Project not found.'), RestController::HTTP_NOT_FOUND);
			} else {
				$title = $this->post('title');
				$deadline = $this->post('deadline');
				if ($title === null || $deadline === null) {
					$this->response(array('status' => 'error', 'message' => 'missing title or deadline.'), RestController::HTTP_BAD_REQUEST);
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
						$this->response(array('status' => 'error', 'error' => validation_errors()), 422);
					} else {
						$timestamp = strtotime($deadline);
						$created = time();
						$id = $this->Tasks_model->add_task($title, $project_id, $created, $timestamp);
						$this->response(array('status' => 'success', 'message' => 'Task created.', 'task_id' => $id), RestController::HTTP_CREATED);
					}

				}
			}
		}
	}

	public function patch_task_patch($id = null)
	{
		$title = $this->patch('title');
		$deadline = $this->patch('deadline');
		$status = $this->patch('status');
		if ($id === null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide task id.'), RestController::HTTP_BAD_REQUEST);
		} else {
			$task = $this->Tasks_model->get_task($id);
			if (!$task) {
				$this->response(array('status' => 'error', 'error' => 'Task not found.'), RestController::HTTP_NOT_FOUND);
			} else {
				if ($title === null || $deadline === null) {
					$this->response(array('status' => 'error', 'error' => 'missing parameters.'), RestController::HTTP_BAD_REQUEST);
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
						$this->response(array('status' => 'error', 'error' => validation_errors()), 422);
					} else {
						$timestamp = strtotime($deadline);
						$updated = time();
						$this->Tasks_model->update_task_in_db($id, $title, $timestamp);
						$this->Tasks_model->update_task_status($id, $status);
						$this->response(array('status' => 'success', 'message' => 'task updated successfully.', 'task_id' => $id, 'updated_at' => $updated), RestController::HTTP_OK);
					}

				}
			}
		}
	}

	public function delete_task_delete($id = null){
		if ($id == null) {
			$this->response(array('status' => 'error', 'error' => 'Please provide an ID'), RestController::HTTP_BAD_REQUEST);
		}
		else{
			$task = $this->Tasks_model->get_task($id);
			if (!$task) {
				$this->response(array('status' => 'error', 'error' => 'Task not found'), RestController::HTTP_NOT_FOUND);
			}
			else{
				$this->Tasks_model->delete_task($id);
				$this->response(array('status' => 'success', 'message' => 'Task deleted successfully.'), RestController::HTTP_OK);
			}
		}
	}


}
