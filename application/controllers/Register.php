<?php



class Register extends CI_Controller{

	public function __construct(){
		parent::__construct();
//		$this->load->helper('url');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');


	}

	public function index(){
		$this->load->view('Register_view');
	}

	public function register(){
		$this->form_validation->set_rules('name','Name','required',
		[
			'required' => 'חסר שם'
		]);
		$this->form_validation->set_rules('email','Email','required|valid_email',[
			'required' => 'חסר אימייל',
			'valid_email'=> 'כתובת לא חוקית'
		]);
		$this->form_validation->set_rules('pass', 'Password', 'required|min_length[5]|max_length[12]',[
			'required'=> 'חסר סיסמא'
		]);

		if ($this->form_validation->run() == FALSE){
			echo json_encode(['status' => 'error',
					'message' => validation_errors()]
			);
		}
		else{
			echo json_encode(['status' => 'success',
					'message' => 'good']
			);
		}
	}

}
