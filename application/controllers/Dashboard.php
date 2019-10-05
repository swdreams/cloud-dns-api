<?php

class Dashboard extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->language('auth');

	}

	public function index()
	{
		$zones_stats = $this->cloudns_sdk->dnsGetZonesStatistics();
		$this->load->vars('zones_stats', $zones_stats);

		$name_servers = $this->cloudns_sdk->dnsAvailableNameServers();
		$this->load->vars('name_servers', $name_servers);

//		$my_ip = $this->cloudns_sdk->getMyIp();
//		if (!isset($my_ip['status']) && !empty($my_ip)) {
//			$my_ip = $my_ip[0];
//		}
//		$this->load->vars('my_ip', $my_ip);

		$this->layout->view('dashboard');
	}


}
