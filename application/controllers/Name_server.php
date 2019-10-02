<?php

class Name_server extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->language('auth');
	}

	public function index()
	{
		$list = $this->cloudns_sdk->dnsAvailableNameServers();
		$this->load->vars('dns_list', $list);

		$this->layout->view('name_server/index');
	}

}
