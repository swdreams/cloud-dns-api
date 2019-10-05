<?php

class Cloud_domain extends MY_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->load->language('auth');

		$this->load->library('pagination');
		$this->load->library('FileDb');

		$controller_name = strtolower(get_class($this));
		$this->ses_key = "ses_$controller_name";
	}

	public function index()
	{
		if ($this->input->method() == 'post' && isset($_POST['zone'])) {
			$zone = $this->input->post('zone');

			$search_data = $this->session->userdata($this->ses_key);
			$search_data['zone'] = $zone;
			$this->session->set_userdata($this->ses_key, $search_data);

			##################################
			####  Download csv file
			##################################
			if ($this->input->post('download_csv') === 'download_csv') {
				$domains = $this->cloudns_sdk->dnsListCloudDomains($zone);
				if (isset($domains['status']) && $domains['status'] == 'Failed') {
					add_flahs_msg($res);
					redirect('/cloud_domain/index/');
				}
				$this->exportCSV($zone, $domains);
			}
		}

		// get zones labels
		$zone_labels = $this->filedb->getLabels();
		$this->load->vars('zone_labels', $zone_labels);

		// get zones
		$zones_list = $this->cloudns_sdk->dnsListZones(1, $this->limit, '');

		$zones = get_option_array($zones_list, 'name', 'name', TRUE);
		if (!empty($zones)) {
			foreach ($zones as $key => $val) {
				$zones[$key] = get_zone_name_with_label($key, $zone_labels);
			}
		}

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

	/**
	 *
	 */
	public function delete_form()
	{
		if ($this->input->method() == 'post') {
			$cloud_names = $this->input->post('cloud_names');

			if (strlen($cloud_names) < 4) {
				$this->flash->error("Please add cloud domain names.");
			} else {
				$names = explode("\n", $cloud_names);

				$bulk_status = array(self::SUCCESS => 0, self::ERROR => 0);
				$success_items = array();
				$failed_items = array();

				if (!empty($names) && count($names) > 0) {

					foreach ($names as $name) {
						if (strlen($name) < 4) continue;
						$res = $this->cloudns_sdk->dnsDeleteCloudDomain($name);
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
						$this->flash->success(sprintf("%s domains deleted: %s", $bulk_status[self::SUCCESS], implode(', ', $success_items)));

					if (count($failed_items) > 0) {
						add_bulk_flash_msg(sprintf("%s domains failed:", $bulk_status[self::ERROR]), $failed_items, self::ERROR);
					}
				} else {
					$this->flash->error("Nothing to delete.");
				}
			}
		}

		$this->layout->view('cloud_domain/delete_form');
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
		if ($this->input->method() == 'post') {
			$zone = $this->input->post('zone');
			$cloud_names = $this->input->post('cloud_names');

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
						$res = $this->cloudns_sdk->dnsAddCloudDomain($zone, $name);

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
					$success_cnt = $bulk_status[self::SUCCESS];
					if ($success_cnt > 0) {
						$msg = '%s';
						if ($success_cnt > self::BULK_MSG_LIMIT) {
							$success_items = array_slice($success_items, 0, self::BULK_MSG_LIMIT);
							$msg .= "...";
						}
						$msg = sprintf($msg, implode(', ', $success_items));

						$this->flash->success(sprintf("%s Master Cloud Domains added: %s", $bulk_status[self::SUCCESS], $msg));
					}

					$failed_cnt = $bulk_status[self::ERROR];
					if ($failed_cnt > 0) {
						add_bulk_flash_msg(sprintf("%s Master Cloud Domains failed:", $bulk_status[self::ERROR]), $failed_items, self::ERROR);
					}
				} else {
					$this->flash->error("Nothing to add.");
				}
			}
		}
		// get zones labels
		$zone_labels = $this->filedb->getLabels();
		$this->load->vars('zone_labels', $zone_labels);

		// get zones
		$zones_list = $this->cloudns_sdk->dnsListZones(1, $this->limit, '');

		$zones = get_option_array($zones_list, 'name', 'name', TRUE);
		if (!empty($zones)) {
			foreach ($zones as $key => $val) {
				$zones[$key] = get_zone_name_with_label($key, $zone_labels);
			}
		}

		$this->load->vars('zones', $zones);

		$this->layout->view('cloud_domain/add');
	}

	// Export data in CSV format
	public function exportCSV($zone, &$data)
	{
		// file name
		$filename = "[$zone]" . '_Cloud_Domains_' . date('Ymd') . '.csv';

		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/csv; ");

		// file creation
		$file = fopen('php://output', 'w');

		$header = array("No", "Cloud Domain");
		fputcsv($file, $header);
		$ind = 1;
		foreach ($data as $row) {
			fputcsv($file, array($ind++, $row));
		}
		fclose($file);
		exit;
	}
}
