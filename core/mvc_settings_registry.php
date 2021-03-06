<?php

class MvcSettingsRegistry {

	var $__settings = array();

	private static function &get_instance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] = new MvcSettingsRegistry();
		}
		return $instance[0];
	}

	public static function &get_settings($key) {
		$_this =& self::get_instance();
		$key = MvcInflector::camelize($key);
		$return = false;
		if (isset($_this->__settings[$key])) {
			$return =& $_this->__settings[$key];
		} else if (class_exists($key)) {
			$_this->__settings[$key] = new $key();
			$return =& $_this->__settings[$key];
		}
		return $return;
	}
	
	public function add_settings($key, &$settings) {
		$_this =& self::get_instance();
		$key = MvcInflector::camelize($key);
		if (!isset($_this->__settings[$key])) {
			$_this->__settings[$key] = $settings;
			return true;
		}
		return false;
	}

}

?>
