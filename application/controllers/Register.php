<?php



class Register extends CI_Controller{

	public function __construct(){
		parent::__construct();
//		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('Register_model');



	}

	public function index(){
		$this->load->view('Register_view');
	}

	public function register(){
		$this->form_validation->set_rules('name','Name','required', array('required' => 'You must provide a %s.'));
	;
		$this->form_validation->set_rules('email','Email','required|valid_email',
			array('required' => 'חסר אימייל',
			'valid_email'=> 'כתובת לא חוקית')
		);
		$this->form_validation->set_rules('pass', 'Password', 'required|min_length[5]|max_length[12]',[
			'required'=> 'חסר סיסמא'
		]);

		if ($this->form_validation->run() == FALSE){
			echo json_encode(['status' => 'error',
					'message' => validation_errors()]
			);
		}
		else{
			$name = $this->input->post('name');
			$email = $this->input->post('email');
			$pass = $this->input->post('pass');
			$id = $this->Register_model->addUser($name, $pass, $email);
//			$this->load->view('Login_view');

			echo json_encode(array('status' => 'success','user_id' => $id)
			);
		}
	}

}
