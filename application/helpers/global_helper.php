<?php
if (!function_exists('get_option_array')) {
	function get_option_array(&$array, $key_field, $value_field, $empty = FALSE, $empty_title = '')
	{
		$ret = array();
		if ($empty) {
			$ret[''] = $empty_title;
		}
		if (!empty($array) && count($array) > 0) {
			foreach ($array as $row) {
				$ret[$row[$key_field]] = $row[$value_field];
			}
		}

		return $ret;
	}
}


if (!function_exists('add_flahs_msg')) {
	function add_flahs_msg(&$res)
	{
		$CI = &get_instance();

		if (isset($res['status'])) {
			if ($res['status'] == 'Failed') {
				$CI->flash->error($res['statusDescription']);
			} else {
				$CI->flash->success(isset($res['statusDescription']) ? $res['statusDescription'] : "Success!");
			}
		}
	}
}

if (!function_exists('check_api_result')) {
	function check_api_result(&$res)
	{
		$ret = array('', '');
		if (isset($res['status'])) {
			if ($res['status'] == 'Failed') {
				$ret = array('error', $res['statusDescription']);
			} else {
				$ret = array('success', isset($res['statusDescription']) ? $res['statusDescription'] : "Success!");
			}
		}

		return $ret;
	}
}


if (!function_exists('add_bulk_flash_msg')) {
	function add_bulk_flash_msg($title, &$data, $type = 'success')
	{
		$ret_msg = $title;
		if (!empty($data) && count($data) > 0) {
			foreach($data as $key => $msg) {
				$ret_msg .= "<br />" . $key . " : " . $msg;
			}
		}

		$CI = &get_instance();
		$CI->flash->$type($ret_msg);
	}
}


if (!function_exists('get_zone_label')) {
	function get_zone_label($zone_name)
	{
		$CI = &get_instance();
		$CI->filedb->getLabels($zone_name);
	}
}

if (!function_exists('get_zone_name_with_label')) {
	function get_zone_name_with_label($zone_name, &$zone_labels)
	{
		$label = isset($zone_labels[$zone_name])?  $zone_labels[$zone_name] : "";
		$label = trim($label);

		return  strlen($label) > 0 ? $zone_name .  htmlspecialchars("<". $label . ">") : $zone_name;
	}
}





