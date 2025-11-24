<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class API extends RestController {
	function __construct() {
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Register_model');
	}


	public function user_post(){
		$name = $this->post('name');
		$email = $this->post('email');
		$password = $this->post('password');
		$data = array('name' => $name, 'email' => $email, 'password' => $password);
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('name', 'Name', 'required', array('required' => 'חסר שם.'));;
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_unique_email',
			array('required' => 'חסר אימייל',
				'valid_email' => 'כתובת לא חוקית')
		);
		$this->form_validation->set_rules(
			'password',
			'Password',
			'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*\d).+$/]',

			array('required' => 'חסר סיסמא',
				'min_length' => 'הסיסמא חייבת להכי לפחות 8 תווים',
				'regex_match' => 'הסיסמא חייבת להכיל אותיות ומספרים')
		);

		if ($this->form_validation->run() == FALSE) {
			$this->response(array('status' => FALSE, 'message' => validation_errors()), 400);
			return;
		}
		else{
			$ID = $this->Register_model->addUser($name, $password, $email);
			log_message('DEBUG',$name.$email .$password);
			$this->response(array('status'=>200,'message'=>'User created successfully.','id'=>$ID), 200);
		}




		$ID = $this->Register_model->addUser($name, $password, $email);
		log_message('DEBUG',$name.$email .$password);
		$response= array('status' => 200, 'message' => "User created successfully", 'id' =>$ID);
		$this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response));


	}
	public function user_get(){
		$id = $this->get('id');
		log_message('DEBUG','api' .$id);

		if ($id === null) {
			$this->response(
				array('status' => 404, 'message' => 'Please provide an ID'),
				RestController::HTTP_BAD_REQUEST
			);
			return;
		}
		$user = $this->Register_model->getUser($id);
		if($user == null){
			$this->response(array('status' => 400, 'message' => 'User not found'),RestController::HTTP_NOT_FOUND);
			return;
		}
		else{
			$this->response(array('status'=>'success','user'=>$user),RestController::HTTP_OK);

		}
	}

	public function user_put(){
		$id = $this->put('id');
		$name = $this->put('name');
		$email = $this->put('email');
		$password = $this->put('password');
		$data = array('name' => $name, 'email' => $email, 'password' => $password);
		log_message('DEBUG',$name.$email .$password);
		$this->form_validation->set_data($data);

		$this->form_validation->set_rules('name', 'Name', 'required', array('required' => 'חסר שם.'));;
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email',
			array('required' => 'חסר אימייל',
				'valid_email' => 'כתובת לא חוקית')
		);
		$this->form_validation->set_rules(
			'password',
			'Password',
			'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*\d).+$/]',

			array('required' => 'חסר סיסמא',
				'min_length' => 'הסיסמא חייבת להכי לפחות 8 תווים',
				'regex_match' => 'הסיסמא חייבת להכיל אותיות ומספרים')
		);
		if ($this->form_validation->run() == FALSE) {
			$this->response((array('status'=>404,'message'=>validation_errors())),RestController::HTTP_NOT_FOUND);
			return;

		}
		else{
			$this->Register_model->updateUser($id, $name, $password, $email);
			$this->response(array('status'=>200,'message'=>'User updated successfully.','id'=>$id), 200);

		}
//		$bool =
	}


	public function login(){
		$name = $this->input->post('name');
		$response = array('status' => 200, 'message' => "User created successfully", 'name' =>$name);
		$this->output->set_status_header(200)->set_content_type('application/json')->set_output(json_encode($response));
	}
	public function unique_email($email)
	{
		if ($this->Register_model->check_email($email)) {
			$this->form_validation->set_message('unique_email', 'האימייל כבר קיים במערכת');
			return FALSE;
		} else {
			return true;
		}

	}

}
