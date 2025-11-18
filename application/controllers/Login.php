<?php

class Login  extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('Login_model');
		$this->load->library('session');
	}
	public function index() {
		$this->load->view('Login_view');
	}

	public function login() {
//		$this->form_validation->set_rules('email', 'Email', 'required|valid_email', array('required' => 'You must provide a %s.'));
//		$this->form_validation->set_rules('pass', 'Password', , array('required' => 'You must provide a %s.'));
//		if($this->form_validation->run() == FALSE) {
//			echo json_encode(array('status' => 'fail', 'message' => validation_errors()));
//		}

			$email  = $this->input->post('email');
			$pass = $this->input->post('pass');
			$response = $this->Login_model->login($email, $pass);
			$response = json_decode($response);
			//login succeed
			if ($response->status == 'success') {
				$this->session->set_userdata(array('user_id' => $response->user_id));
				echo json_encode(array('status' => 'success', 'message' => $response->user_id));
			}
			//user not found
			elseif ($response->status == 'fail') {
				echo json_encode(array('status' => 'fail', 'message' => 'משתמש לא קיים'));
			}
			//wrong password
			elseif ($response->status == 'error') {
				echo json_encode(array('status' => 'fail', 'message' => 'סיסמא שגויה'));
			}
			else{
				echo json_encode($response);
			}
		}
		public function logout() {
		$user_id = $_SESSION['user_id'];
		if($this->Login_model->logout($user_id)){
			unset($_SESSION['user_id']);
			echo json_encode(array('status' => 'success'));
		}

		}


}
