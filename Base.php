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
	 * @param boolean|array $prefixes True for default (`action_`/`filter_`), array with keys "action" & "filter" or false
	 * @param string|object $parent Object to register from
	 */
	protected static function _register_hooks($prefixes, $parent) {
		$enable_prefixes = true;
		if ($prefixes === false) {
			$enable_prefixes = false;
		}
		elseif ($prefixes === true) {
			$prefixes = array('filter' => 'filter_', 'action' => 'action_');
		}

		$self = new ReflectionClass($parent);
		foreach ($self->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
			$params = $method->getNumberOfParameters();
			$doc = $method->getDocComment();
			if (!empty($doc) && preg_match('#^\s+\*\s*@wp-nohook#im', $doc) !== 0) {
				continue;
			}

			$hooks = array('filter' => array(), 'action' => array());

			if ($enable_prefixes === true) {
				// If either prefix is blank, always hook
				if ($prefixes['filter'] === '' || $prefixes['action'] === '') {
					$hooks['filter'][$method->name] = 10;
				}

				// Method starts with filter prefix
				elseif ($enable_prefixes === true && strpos($method->name, $prefixes['filter']) === 0) {
					$hook = substr($method->name, strlen($prefixes['filter']));
					$hooks['action'][$hook] = 10;
				}

				// Method starts with action prefix
				elseif ($enable_prefixes === true && strpos($method->name, $prefixes['action']) === 0) {
					$hook = substr($method->name, strlen($prefixes['action']));
					$hooks['action'][$hook] = 10;
				}
			}

			// If we haven't hooked anything yet, check phpdoc
			if (empty($hooks['filter']) && empty($hooks['action'])) {
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
