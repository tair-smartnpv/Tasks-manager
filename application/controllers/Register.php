<?php


class Register extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('Users_model');
		$this->load->model('ApiKeys_model');
		$this->load->library('email');
		$this->load->library('session');


	}

	public function index()
	{
		$this->load->view('Register_view');
	}

	public function register()
	{


		$this->form_validation->set_rules('name', 'Name', 'required', array('required' => 'חסר שם.'));;
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email',//|callback_unique_email
			array('required' => 'חסר אימייל',
				'valid_email' => 'כתובת לא חוקית')
		);
		$this->form_validation->set_rules(
			'pass',
			'Password',
			'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*\d).+$/]',

			array('required' => 'חסר סיסמא',
				'min_length' => 'הסיסמא חייבת להכי לפחות 8 תווים',
				'regex_match' => 'הסיסמא חייבת להכיל אותיות ומספרים')
		);

		if ($this->form_validation->run() == FALSE) {
			echo json_encode(array('status' => 'error',
					'message' => array(
						'name' => form_error('name'),
						'email' => form_error('email'),
						'pass' => form_error('pass'),

					))
			);
		} else {
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$pass = $this->input->post('pass');
			if ($this->Users_model->email_exist($email)) {
				log_message("DEBUG", 'Email exist');
				$this->session->set_flashdata('message', 'חשבון קיים. באפשרותך להתחבר');
//				redirect('Login/index');
				echo json_encode(array('status' => 'error','message'=>'email already exist'));
			} else {
				$id = $this->Users_model->add_user($name, $pass, $email);
				$this->send_email($email);


				$key = $this->ApiKeys_model->add_user($id);

				echo json_encode(array('status' => 'success', 'user_id' => $id, 'key' => $key)
		);
		}}
	}

	public function unique_email($email)
	{
		if ($this->Users_model->email_exist($email)) {
			$this->form_validation->set_message('unique_email', 'האימייל כבר קיים במערכת');
			return FALSE;
		} else {
			return true;
		}

	}

	public function send_email($email)
	{
		$this->email->to($email);
		$this->emil->from('tair@test.com');
		$this->email->subject();
		$this->email->message();
		if ($this->email->send()) {
			return true;
		} else {
			log_message('error', $this->email->print_debugger());
			return false;
		}
	}

}
