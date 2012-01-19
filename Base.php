<?php

/**
 * A new way of using the WordPress API
 *
 * @package Rotor WP Plugin
 */
abstract class Rotor_WPPlugin_Base {
	private $methods;

	/**
	 * Register hooks
	 *
	 * @see Rotor_WP_Plugin::__construct
	 * @see Rotor_WP_Plugin::register_hooks
	 * @param boolean $enable_prefixes Whether to enable prefixed methods (i.e. `action_init` or `filter_the_title`)
	 * @param string|object $parent Object to register from
	 */
	protected static function _register_hooks($enable_prefixes, $parent) {
		$self = new ReflectionClass($parent);
		foreach ($self->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			$params = $method->getNumberOfParameters();
			$doc = $method->getDocComment();
			if (!empty($doc) && preg_match('#^\s+\*\s*@wp-nohook#im', $doc) !== 0) {
				continue;
			}

			$hooks = array('filter' => array(), 'action' => array());

			if ($enable_prefixes === true && strpos($method->name, 'filter_') === 0) {
				$hook = substr($method->name, 7);
				$hooks['action'][$hook] = 10;
			}
			elseif ($enable_prefixes === true && strpos($method->name, 'action_') === 0) {
				$hook = substr($method->name, 7);
				$hooks['action'][$hook] = 10;
			}
			else {
				if (empty($doc) || (strpos($doc, '@wp-filter') === false && strpos($doc, '@wp-action') === false)) {
					continue;
				}

				preg_match_all('#^\s+\*\s*@wp-(action|filter)\s+([\w-]+)(\s*\d+)?#im', $doc, $matches, PREG_SET_ORDER);
				if (empty($matches)) {
					continue;
				}
				foreach ($matches as $match) {
					$type = $match[1];
					$hook = $match[2];
					$priority = 10;
					if (!empty($match[3])) {
						$priority = (int) $match[3];
					}

					$hooks[$type][$hook] = $priority;
				}
			}

			foreach ($hooks['filter'] as $hook => $priority) {
				call_user_func(array($parent, 'add_filter'), $hook, $method->name, $priority, $params, $parent);
			}
			foreach ($hooks['action'] as $hook => $priority) {
				call_user_func(array($parent, 'add_action'), $hook, $method->name, $priority, $params, $parent);
			}
		}
	}
}
