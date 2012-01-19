<?php

if (!class_exists('Rotor_WPPlugin_Base')) {
	require_once(dirname(__FILE__) . '/Base.php');
}

/**
 * A new way of using the WordPress API
 *
 * @package Rotor WP Plugin
 */
class Rotor_WPPlugin extends Rotor_WPPlugin_Base {
	/**
	 * Constructor
	 *
	 * Ensure you call this from your child class
	 *
	 * @param boolean $enable_prefixes Whether to enable prefixed methods (i.e. `action_init` or `filter_the_title`)
	 */
	protected function __construct($enable_prefixes = false) {
		$this->_register_hooks($enable_prefixes, $this);
	}

	/**
	 * Add a method as a filter
	 *
	 * This is exactly the same as {@see add_filter()} but instead of passing
	 * a full callback, only the method needs to be passed in.
	 *
	 * @param string $hook Filter name
	 * @param string $method Method name on current class
	 * @param int $priority Specify the order in which the functions associated with a particular action are executed (default: 10)
	 * @param int $accepted_args Number of parameters which callback accepts (default: corresponds to method prototype)
	 */
	protected function add_filter($hook, $method, $priority = 10, $params = null) {
		if (!method_exists($this, $method)) {
			throw new InvalidArgumentException('Method does not exist');
		}

		if ($params === null) {
			$ref = new ReflectionMethod($this, $method);
			$params = $ref->getNumberOfParameters();
		}

		return add_filter($hook, array($this, $method), $priority, $params);
	}

	/**
	 * Add a method as a action
	 *
	 * This is exactly the same as {@see add_action()} but instead of passing
	 * a full callback, only the method needs to be passed in.
	 *
	 * @internal This is duplication, but ensures consistency with WordPress API
	 * @param string $hook Action name
	 * @param string $method Method name on current class
	 * @param int $priority Specify the order in which the functions associated with a particular action are executed (default: 10)
	 * @param int $accepted_args Number of parameters which callback accepts (default: corresponds to method prototype)
	 */
	protected function add_action($hook, $method, $priority = 10, $params = null) {
		if (!method_exists($this, $method)) {
			throw new InvalidArgumentException('Method does not exist');
		}

		if ($params === null) {
			$ref = new ReflectionMethod($this, $method);
			$params = $ref->getNumberOfParameters();
		}

		return add_action($hook, array($this, $method), $priority, $params);
	}
}
