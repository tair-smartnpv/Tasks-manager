<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';
require APPPATH . 'libraries/Format.php';

use chriskacerguis\RestServer\RestController;

class APIUsers extends RestController
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Users_model');
	}

	public function get_all_users_get()
	{
		log_message('DEBUG', 'get_all_users_get()');
		$result = $this->Users_model->get_users_list();
		if ($result) {
			$this->response(array('code' => 200, 'status' => 'success', 'message' => 'Get users successfully', 'data' => $result), RestController::HTTP_OK);
		} else {
			$this->response(array('code' => 404, 'status' => 'error', 'error' => 'No users found'), RestController::HTTP_NOT_FOUND);

		}

	}

	public function get_user_get()
	{
		$api_key = $this->input->get_request_header('X-API-KEY');
		$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;

		log_message('DEBUG', 'api' . $user_id);

//		if ($id === null) {
//			$this->response(
//				array('code'=>400,'status' => 'error', 'error' => 'Please provide an ID'),
//				RestController::HTTP_BAD_REQUEST
//			);
//			return;
//		}
		$user = $this->Users_model->get_user($user_id);
		if (!$user) {
			$this->response(array('code' => 404, 'status' => 'error', 'message' => 'User not found'), RestController::HTTP_NOT_FOUND);
		} else {
			$this->response(array('code' => 200, 'status' => 'success', 'message' => 'Get user successfully.', 'data' => $user), RestController::HTTP_OK);

		}
	}

	public function post_user_post()
	{
		$name = $this->post('name');
		$email = $this->post('email');
		$password = $this->post('password');
		if ($name === null || $email === null || $password === null) {
			$this->response(array('status' => 'error', 'error' => 'Please fill all fields'), RestController::HTTP_BAD_REQUEST);
		} else {
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
				$this->response(array('code' => 422, 'status' => 'error', 'error' => validation_errors()), 422);
				return;
			} else {
				$id = $this->Users_model->add_user($name, $password, $email);
				$key = bin2hex(random_bytes(20));
				log_message('DEBUG', 'API Key:' . $key);

				$this->db->insert('keys', array(
					'user_id' => $id,
					'key' => $key,
					'level' => 1,
					'ignore_limits' => 0,
					'is_private_key' => 0,
					'date_created' => time()
				));

				log_message('DEBUG', $name . $email . $password);
				$this->response(array('code' => 200,
					'status' => 'success', 'message' => 'User created successfully.', 'data' => array(
						'name' => $name, 'email' => $email, 'key' => $key)), RestController::HTTP_CREATED);
			}

		}

	}

	public function patch_user_patch()
	{
		$api_key = $this->input->get_request_header('X-API-KEY');
		$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;

		$name = $this->patch('name');
		$email = $this->patch('email');
		$password = $this->patch('password');
		if ($name === null || $email === null || $password === null) {
			$this->response(array('status' => 'error', 'error' => 'Please fill all fields'), RestController::HTTP_BAD_REQUEST);
		} else {
			$data = array('name' => $name, 'email' => $email, 'password' => $password);
			log_message('DEBUG', $name . $email . $password);
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
				$this->response((array('code' => 422, 'status' => 'error', 'error' => validation_errors())), 422);
				return;

			} else {
				$user = $this->Users_model->update_user($user_id, $name, $password, $email);
				$this->response(array('code' => 200, 'status' => 'success', 'message' => 'User updated successfully.',
					'data' => array('name' => $name, 'email' => $email)), RestController::HTTP_OK);
			}

		}
	}


	public function delete_user_delete()
	{
		$api_key = $this->input->get_request_header('X-API-KEY');
		$user_id = $this->ApiKeys_model->get_user($api_key)->user_id;

		$user = $this->Users_model->get_user($user_id);
		if (!$user) {
			$this->response(array('code'=>404,'status' => 'error', 'error' => 'User not found'), RestController::HTTP_NOT_FOUND);
		} else {
			$this->Users_model->delete_user($user_id);
			$this->response(array('code'=>200,'status' => 'success', 'message' => 'User deleted successfully.'), RestController::HTTP_OK);

		}
	}


	public function unique_email($email)
	{
		if ($this->Users_model->check_email($email)) {
			$this->form_validation->set_message('unique_email', 'נראה שאתה משתמש רשום.');
			return FALSE;
		} else {
			return true;
		}

	}

}
