<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->library('form_validation');
		$this->load->language('auth');
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'auth'), $this->config->item('error_end_delimiter', 'auth'));
	}

	// redirect if needed, otherwise display the user list
	public function index()
	{

		if (!$this->dns_auth->logged_in()) {
			// redirect them to the login page
			redirect('auth/login', 'refresh');
		} elseif (!$this->dns_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
		{
			// redirect them to the home page because they must be an administrator to view this
			return show_error('You must be an administrator to view this page.');
		} else {
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			//list the users
			$this->data['users'] = $this->dns_auth->users()->result();
			foreach ($this->data['users'] as $k => $user) {
				$this->data['users'][$k]->groups = $this->dns_auth->get_users_groups($user->id)->result();
			}

			$this->_render_page('auth/index', $this->data);
		}
	}

	// log the user in
	public function login()
	{
		$this->data['title'] = lang('login_heading');

		$this->layout->setLayout('/layouts/public');

		if ($this->input->post('user_id')) {
			//validate form input
			$this->form_validation->set_rules('user_id', str_replace(':', '', $this->lang->line('login_identity_label')), 'required');
			$this->form_validation->set_rules('password', str_replace(':', '', $this->lang->line('login_password_label')), 'required');

			if ($this->form_validation->run() == true) {
				// check to see if the user is logging in

				if ($this->dns_auth->login($this->input->post('user_id'), $this->input->post('password'), FALSE)) {
					//if the login is successful
					//redirect them back to the home page
					$this->session->set_flashdata('message', lang('login_successful'));
					redirect('dashboard', 'index');
				} else {

					// if the login was un-successful
					$this->session->set_flashdata('message', lang('login_unsuccessful'));
					//redirect('auth/login', 'refresh'); // use redirects instead of loading views for compatibility with MY_Controller libraries
				}
			}

			// the user is not logging in so display the login page
			// set the flash data error message if there is one
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');
		}


		$this->data['user_id'] = array(
			'name' => 'user_id',
			'id' => 'user_id',
			'type' => 'text',
			'value' => $this->form_validation->set_value('user_id'),
			'class' => 'form-control',
			'placeholder' => 'Username'
		);

		$this->data['password'] = array(
			'name' => 'password',
			'id' => 'password',
			'type' => 'password',
			'class' => 'form-control',
			'placeholder' => 'Password'
		);

		$this->data['content'] = 'auth/login';

		$this->layout->view('auth/login', $this->data);

	}

	// log the user out
	public function logout()
	{
		$this->data['title'] = "Logout";

		// log the user out
		$logout = $this->dns_auth->logout();

		// redirect them to the login page
		$this->session->set_flashdata('message', lang('logout_successful'));
		redirect('auth/login', 'refresh');
	}

	public function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	public function _valid_csrf_nonce()
	{
		$csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
		if ($csrfkey && $csrfkey == $this->session->flashdata('csrfvalue')) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function _render_page($view, $data = null, $returnhtml = false)//I think this makes more sense
	{

		$this->viewdata = (empty($data)) ? $this->data : $data;

		$view_html = $this->load->view($view, $this->viewdata, $returnhtml);

		if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
	}

}
