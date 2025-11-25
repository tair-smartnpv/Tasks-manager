<?php

defined('BASEPATH') OR exit('No direct script access allowed.');


require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';
use chriskacerguis\RestServer\RestController;

class APITasks extends RestController{

	function __construct(){
		parent::__construct();
		$this->load->model('Tasks_model');
		$this->load->library('form_validation');
	}

	public function get_all_tasks_get(){
		$result = $this->Tasks_model->get_tasks();
		if($result){
			$this->response(array('data'=>$result),RestController::HTTP_OK);
		}
		else{
			$this->response(array('status'=>'error'),RestController::HTTP_NOT_FOUND);
		}

	}

	public function get_task_get(){
		$id = $this->get('id');
		if($id=== null){
			$this->response(array('status'=>'error','message'=>'missing id.'),RestController::HTTP_BAD_REQUEST);
		}
		else{
			$task = $this->Tasks_model->get_task($id);
			if($task){
				$this->response(array('data'=>$task),RestController::HTTP_OK);
			}
			else{
				$this->response(array('status'=>'error','message'=>'Task not exist'),RestController::HTTP_NOT_FOUND);
			}
		}
	}

	public function get_tasks_by_project_get(){
		$project_id = $this->get('id');
		if($project_id=== null){
			$this->response(array('status'=>'error','message'=>'missing id.'),RestController::HTTP_BAD_REQUEST);
		}
		else{
			$tasks  = $this->Tasks_model->get_tasks_by_project($project_id);
			if($tasks){
				$this->response(array('data'=>$tasks),RestController::HTTP_OK);
			}
			else{
				$this->response(array('status'=>'error','message'=>'Project missing or has not tasks'),RestController::HTTP_NOT_FOUND);

			}
		}
	}

	public function create_task_post(){
		$title = $this->post('title');
		$deadline = $this->post('deadline');
		$project_id = $this->post('project_id');
		if($title=== null||$deadline=== null){
			$this->response(array('status'=>'error','message'=>'missing title or deadline.'),RestController::HTTP_BAD_REQUEST);
		}
		else{
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
				echo json_encode(array('status' => 'error', 'message' => validation_errors()));
			}
			else{
				$timestamp = strtotime($deadline);
				$created = time();
				$id = $this->Tasks_model->add_task($title,$project_id,$created,$timestamp);
				$this->response(array('status'=>'ok','message'=>'task added successfully.','task id'=>$id),RestController::HTTP_OK);
			}


		}
	}
	public function update_task_patch($id=null){
		$title = $this->patch('title');
		$deadline = $this->patch('deadline');
		$status = $this->patch('status');
		if($id=== null){
			$this->response(array('status'=>'error','message'=>'missing id.'),RestController::HTTP_BAD_REQUEST);
		}
		else{
			if($title=== null||$deadline=== null){
				$this->response(array('status'=>'error','message'=>'missing parameters.'),RestController::HTTP_BAD_REQUEST);
			}
			else{
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
					$this->response(array('status'=>'error','message' => validation_errors()),RestController::HTTP_BAD_REQUEST);
				}
				else{
					$timestamp = strtotime($deadline);
					$updated = time();
					if($this->Tasks_model->update_task_in_db($id,$title,$timestamp) && $this->Tasks_model->update_task_status($id,$status)){
						$this->response(array('status'=>'ok','message'=>'task updated successfully.','task id'=>$id,'updated'=>$updated),RestController::HTTP_OK);

					}
					else{
						$this->response(array('status'=>'error','message'=>'task not exist'),RestController::HTTP_NOT_FOUND);
					}

				}
			}
		}





	}



}
