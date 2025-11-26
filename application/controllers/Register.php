<?php


class Register extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('Users_model');


	}

	public function index()
	{
		$this->load->view('Register_view');
	}

	public function register()
	{

		$this->form_validation->set_rules('name', 'Name', 'required', array('required' => 'חסר שם.'));;
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_unique_email',
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
			$id = $this->Users_model->add_user($name, $pass, $email);
			$key = bin2hex(random_bytes(20));
			log_message('DEBUG','API Key:' . $key);

			$this->db->insert('keys', array(
				'user_id' => $id,
				'key' => $key,
				'level' => 1,
				'ignore_limits' => 0,
				'is_private_key' => 0,
				'date_created' => time()
			));

			echo json_encode(array('status' => 'success', 'user_id' => $id, 'key' => $key)
			);
		}
	}

	public function unique_email($email)
	{
		if ($this->Users_model->check_email($email)) {
			$this->form_validation->set_message('unique_email', 'האימייל כבר קיים במערכת');
			return FALSE;
		} else {
			return true;
		}

	}

}
