<?php
if (!function_exists('get_option_array')) {
	function get_option_array(&$array, $key_field, $value_field, $empty=FALSE, $empty_title='')
	{
		$ret = array();
		if ($empty) {
			$ret[''] = $empty_title;
		}
		if (!empty($array) && count($array) > 0) {
			foreach($array as $row) {
				$ret[$row[$key_field]] = $row[$value_field];
			}
		}

		return $ret;
	}
}


if (!function_exists('add_flahs_msg')) {
	function add_flahs_msg(&$res)
	{
		$CI = & get_instance();

		if (isset($res['status'])) {
			if ($res['status'] == 'Failed') {
				$CI->flash->error($res['statusDescription']);
			} else {
				$CI->flash->success(isset($res['statusDescription'])? $res['statusDescription'] : "Success!");
			}
		}
	}
}
