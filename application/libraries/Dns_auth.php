<?php

class Dns_auth
{
	public function __construct()
	{
	}

	/**
	 * __get
	 *
	 * Enables the use of CI super-global without having to define an extra variable.
	 *
	 *
	 * @access    public
	 * @param    $var
	 * @return    mixed
	 */
	public function __get($var)
	{
		return get_instance()->$var;
	}

	/**
	 * login
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function login($user_id, $password, $remember = FALSE)
	{
		if (empty($user_id) || empty($password)) {
			//$this->set_error('login_unsuccessful');
			return FALSE;
		}

		$user = $this->user($user_id);

		if ($user) {
			if ($password === $user['password']) {
				$this->set_session($user);
				//$this->set_message('login_successful');
				return TRUE;
			}
		}

		//$this->set_error('login_unsuccessful');
		return FALSE;
	}

	/**
	 * logout
	 *
	 * @return void
	 * @author Mathew
	 **/
	public function logout()
	{
		$identity = 'user_id';

		if (substr(CI_VERSION, 0, 1) == '2') {
			$this->session->unset_userdata(array($identity => '', 'id' => '', 'user_id' => ''));
		} else {
			$this->session->unset_userdata(array($identity, 'id', 'user_id'));
		}

		// Destroy the session
		$this->session->sess_destroy();

		//Recreate the session
		if (substr(CI_VERSION, 0, 1) == '2') {
			$this->session->sess_create();
		} else {
			if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
				session_start();
			}
			$this->session->sess_regenerate(TRUE);
		}

		//$this->set_message('logout_successful');
		return TRUE;
	}

	/**
	 * logged_in
	 *
	 * @return bool
	 * @author Mathew
	 **/
	public function logged_in()
	{
		return $this->get_user_id();
	}

	/**
	 * logged_in
	 *
	 * @return integer
	 * @author jrmadsen67
	 **/
	public function get_user_id()
	{
		$user_id = $this->session->userdata('user_id');
		if (!empty($user_id)) {
			return $user_id;
		}
		return null;
	}

	/**
	 * user
	 *
	 * @return object
	 * @author Ben Edmunds
	 **/
	public function user($id = NULL)
	{
		// if no id was passed use the current users id
		$id = isset($id) ? $id : $this->session->userdata('user_id');

		$users = $this->config->item('users');

		foreach ($users as $user) {
			$user['user_id'] == $id;
			return $user;
		}
		return NULL;
	}

	/**
	 * is_admin
	 *
	 * @return bool
	 * @author Ben Edmunds
	 **/
	public function is_admin($id = false)
	{
		return true;
	}

	/**
	 * set_session
	 *
	 * @return bool
	 * @author jrmadsen67
	 **/
	public function set_session($user)
	{
		$this->session->set_userdata($user);

		return TRUE;
	}

}
