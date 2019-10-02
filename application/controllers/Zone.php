<?php

class Zone extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->language('auth');

	}

	public function index()
	{
		$data = array();

		$zones = $this->cloudns_sdk->dnsListZones(1, 20, '');
		$this->load->vars('zones', $zones);

		$this->layout->view('zone/index');
	}

	public function view($name)
	{
		$data = array();

		$zone_info = $this->cloudns_sdk->dnsGetZoneInformation($name);
		$this->load->vars('zone_info', $zone_info);

		$this->layout->view('zone/view');
	}

}
