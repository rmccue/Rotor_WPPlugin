<?php
require_once('Static.php');

class ExampleStaticPlugin extends Rotor_WPPlugin_Static {
	public static function bootstrap() {
		self::register_hooks();
	}

	/**
	 * @wp-action init
	 */
	public static function init($a) {
		echo 'init!';
	}
}

ExampleStaticPlugin::bootstrap();