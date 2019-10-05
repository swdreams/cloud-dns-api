<?php

class Zone extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->language('auth');
		$this->load->library('pagination');

		$controller_name = strtolower(get_class($this));
		$this->ses_key = "ses_$controller_name";

		$this->load->library('FileDb');
	}

	public function index()
	{
		###################################################
		#	Search
		###################################################

		if ($this->input->method() == 'post' && isset($_POST['zone_search'])) {
			$zone_search = $this->input->post('zone_search');

			$params = $this->session->userdata($this->ses_key);
			$params['zone_search'] = $zone_search;
			$this->session->set_userdata($this->ses_key, $params);

			redirect('/zone/index');
		}

		// Get the saved search zone from session.
		$params = $this->session->userdata($this->ses_key);
		$zone_search = isset($params['zone_search']) ? $params['zone_search'] : '';

		$page_cnt = $this->cloudns_sdk->dnsGetPagesCount($this->limit, $zone_search);
		$cur_page = 1;

		###################################################
		#	Get the zones list now
		###################################################
		$zone_labels = $this->filedb->getLabels();
		$this->load->vars('zone_labels', $zone_labels);


		$zones = $this->cloudns_sdk->dnsListZones($cur_page, $this->limit, $zone_search);
		$this->load->vars('zones', $zones);

		$total_count = (!empty($zones) && count($zones) > 0)? count($zones) : 0;
		$this->load->vars('total_count', $total_count);

		$this->load->vars('zone_search', $zone_search);

		$this->layout->view('zone/index');
	}

	public function ajax_save_labels()
	{
		$success = TRUE;
		$msg = "";
		if ($this->input->method() == 'post'){
			$new_labels = json_decode($_POST['json'], TRUE);

			$labels = $this->filedb->getLabels();

			$labels = array_merge($labels, $new_labels);

			$this->filedb->writeLabels($labels);
		} else {
			$success = FALSE;
			$msg = 'Invalid call';
		}

		$response['success'] = $success;
		$response['msg'] = $msg;
		header('Content-Type: application/json');

		echo json_encode($response);
		exit;
	}

	public function view($name)
	{
		$data = array();

		$zone_info = $this->cloudns_sdk->dnsGetZoneInformation($name);
		$this->load->vars('zone_info', $zone_info);

		$this->layout->view('zone/view');
	}

	public function add()
	{
		if ($this->input->method() == 'post') {
			$cloud_names = $this->input->post('zone_names');
			$zone_type = $this->input->post('zone_type');

			if (strlen($cloud_names) < 4) {
				$this->flash->error("Please add a cloud domain name at least.");
			} else {

				$names = explode("\n", $cloud_names);

				$bulk_status = array(self::SUCCESS => 0, self::ERROR => 0);
				$success_items = array();
				$failed_items = array();

				if (!empty($names) && count($names) > 0) {
					foreach ($names as $name) {
						if (strlen($name) < 4) continue;
						$res = $this->cloudns_sdk->dnsRegisterDomainZone($name, $zone_type);

						list($status, $msg) = check_api_result($res);
						if ($status == self::SUCCESS) {
							$bulk_status[self::SUCCESS]++;
							$success_items[] = $name;
						} else {
							$bulk_status[self::ERROR]++;
							$failed_items[$name] = $msg;
						}
					}
				}

				if (!empty($success_items) || !empty($failed_items)) {
					if (count($success_items) > 0)
						$this->flash->success(sprintf("%s domains added: %s", $bulk_status[self::SUCCESS], implode(', ', $success_items)));

					if (count($failed_items) > 0) {
						add_bulk_flash_msg(sprintf("%s domains failed:", $bulk_status[self::ERROR]), $failed_items, self::ERROR);
					}
				} else {
					$this->flash->error("Nothing to add.");
				}
			}

			redirect("zone");
		}

		$this->layout->view('zone/add');
	}

	public function delete($name)
	{
		$res = $this->cloudns_sdk->dnsDeleteDomainZone($name);
		add_flahs_msg($res);

		redirect("/zone/index");
	}

}
