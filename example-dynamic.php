<?php
require_once('plugin.php');

class ExampleDynamicPlugin extends Rotor_WPPlugin {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * @wp-action init
	 */
	public function init($a) {
		echo 'init!';
	}
}

$plugin = new ExampleDynamicPlugin();