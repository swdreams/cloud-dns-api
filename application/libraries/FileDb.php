<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class FileDb
{
	const MAX_FILE_SIZE = 101024;    // File Max Size : 10kb

	var $fdb_labels_path = '';
	var $labels = array();

	public function __construct()
	{
		$this->fdb_labels_path = $this->config->item('fdb_labels');
		$this->initLabels();
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

	public function initLabels()
	{
		try {
			if (!file_exists($this->fdb_labels_path)) {
				$fp = fopen($this->fdb_labels_path, 'w');
				if ($fp) {
					fwrite($fp, json_encode(array()));
					fclose($fp);
				}
			}
		} catch (Exception $e) {
			print_r($e);
		}
	}

	public function getLabels($zone_name = NULL)
	{
		$this->labels = array();
		try {
			$fp = fopen($this->fdb_labels_path, 'r');
			if ($fp) {
				$content = fread($fp, self::MAX_FILE_SIZE);
				if (strlen($content) > 6) {
					$this->labels = json_decode($content, TRUE);
				}
				fclose($fp);
			} else {
				print("File Operation Failed");
			}
		} catch (Exception $e) {
			print_r($e);
		}

		if ($zone_name != NULL && isset($this->labels[$zone_name]))
			return $this->labels[$zone_name];

		return $this->labels;
	}

	public function writeLabels($labels)
	{
		try {
			$fp = fopen($this->fdb_labels_path, 'w');
			if ($fp) {
				if (!empty($labels)) {
					foreach($labels as $zone => $label) {
						$label = trim($label);
						if (strlen($label) < 1) {
							unset($labels[$zone]);
						}
					}
				}
				fwrite($fp, json_encode($labels));
				fclose($fp);
			} else {
				print("File Operation Failed");
			}
		} catch (Exception $e) {
			print_r($e);
		}
	}
}
