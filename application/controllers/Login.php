<?php

class Login extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('Users_model');
		$this->load->library('session');
	}

	public function index()
	{
		$this->load->view('Login_view');
	}

	public function login()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email',
			array('required' => 'חסר אימייל.',
				'valid_email' => 'כתובת אימייל לא חוקית'
			));
		$this->form_validation->set_rules('password', 'Password', 'required',
			array('required' => 'חסרה סיסמא.'));
		if ($this->form_validation->run() == FALSE) {
			echo json_encode(array('status' => 'fail',
				'message' => array(
					'email' => form_error('email'),
					'password' => form_error('password'),
				)));
			return;
		} else {
			$email = $this->input->post('email');
			$pass = $this->input->post('password');

			//login succeed
			if (!$this->Users_model->user_exists($email)) {
				echo json_encode(array('status' => 'fail','message'=> array(
					'email' => 'משתמש לא נמצא')));
				return;
			} //wrong password
			if (!$this->Users_model->correct_password($email, $pass)) {
				echo json_encode(array('status' => 'fail', 'message' => array(
					'password' => 'סיסמא שגויה'
				)));
				return;
			}
			$response = $this->Users_model->login($email, $pass);
			$user_id = $response['user_id'];
			$username = $response['name'];
			$key = $this->db->get_where('keys', array('user_id' => $user_id))->row()->key;

			$this->session->set_userdata(array('user_id' => $user_id));
			$this->session->set_userdata(array('username' => $username));
			echo json_encode(array('status' => 'success', 'message' => $user_id,'api_key' => $key));

		}
	}


	public function logout()
	{
		$user_id = $_SESSION['user_id'];
		if ($this->Users_model->logout($user_id)) {
			unset($_SESSION['user_id']);
			unset($_SESSION['username']);
			echo json_encode(array('status' => 'success'));
		}

	}


}
