<?php

class Cloud_domain extends MY_Controller
{
	var $ses_key = "";

	function __construct()
	{
		parent::__construct();

		$this->load->language('auth');

		$this->load->library('pagination');

		$controller_name=strtolower(get_class($this));
		$this->ses_key = "ses_$controller_name";
	}

	public function index()
	{
		if ($this->input->method() == 'post' && isset($_POST['zone'])) {
			$zone = $this->input->post('zone');
			//$zone_search = $this->input->post('zone_search');

			$search_data = $this->session->userdata($this->ses_key);
			$search_data['zone'] = $zone;
			$this->session->set_userdata($this->ses_key, $search_data);
		}

		// get zones
		$zones_list = $this->cloudns_sdk->dnsListZones(1, $this->limit, '');
		$zones = get_option_array($zones_list, 'name', 'name', TRUE);
		$this->load->vars('zones', $zones);

		// Get the saved search zone from session.
		$search_data = $this->session->userdata($this->ses_key);
		$selected_zone = isset($search_data['zone']) ? $search_data['zone'] : '';

		$domains = array();
		if ($selected_zone) {
			$domains = $this->cloudns_sdk->dnsListCloudDomains($selected_zone);
			if (isset($domains['status']) && $domains['status'] == 'Failed') {
				$domains = array();
				unset($search_data['zone']);
				$this->session->set_userdata($this->ses_key, $search_data);
			}
		}


		$this->load->vars('domains', $domains);
		$this->load->vars('total_count', count($domains));
		$this->load->vars('selected_zone', $selected_zone);
		$this->load->vars('search_data', $search_data);

		//$this->flash->success("OK.");

		$this->layout->view('cloud_domain/index');
	}

	public function view($name)
	{
		$zone_info = $this->cloudns_sdk->dnsGetZoneInformation($name);
		$this->load->vars('zone_info', $zone_info);

		$this->layout->view('cloud_domain/view');
	}

	public function change_form()
	{
		if ($this->input->method() == 'post' && isset($_POST['domain_name'])) {
			$domain_name = $this->input->post('domain_name');
			$res = $this->cloudns_sdk->dnsChangeCloudMaster($domain_name);

			add_flahs_msg($res);
		}

		$this->layout->view('cloud_domain/change_form');
	}

	public function delete($name)
	{
		$res = $this->cloudns_sdk->dnsDeleteCloudDomain($name);
		add_flahs_msg($res);

		redirect("/cloud_domain/index");
	}

	public function change_master($name)
	{
		$res = $this->cloudns_sdk->dnsChangeCloudMaster($name);
		add_flahs_msg($res);

		redirect("/cloud_domain/index");
	}

	public function add()
	{
		$zones = $this->cloudns_sdk->dnsListZones(1, $this->limit, '');

		if ($this->input->method() == 'post') {
			$zone = $this->input->post('zone');
			$cloud_names = $this->input->post('cloud_names');

			if (strlen($cloud_names) < 4) {
				$this->flash->error("Please add a cloud domain names.");
			} else {
				$names = explode("\n", $cloud_names);

				if (!empty($names) && count($names) > 0) {
					foreach ($names as $name) {
						if (strlen($name) < 4) continue;
						$res = $this->cloudns_sdk->dnsAddCloudDomain($zone, $name);
						add_flahs_msg($res);
					}
				}
			}
		}

		$zones = get_option_array($zones, 'name', 'name');
		$this->load->vars('zones', $zones);

		$this->layout->view('cloud_domain/add');
	}

}
