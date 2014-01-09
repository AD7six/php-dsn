<?php

namespace AD7six\Envy;

class Env {

/**
 * allByPrefix
 *
 * @param string $prefix
 * @param string $defaultKey
 * @return array
 */
	public static function allbyPrefix($prefix, $defaultKey = 'default') {
		if (!$prefix) {
			return [];
		}

		$values = $_ENV + $_SERVER;
		$keys = array_keys($values);

		$return = [];
		foreach($keys as $key) {
			if (strpos($key, $prefix) !== 0) {
				continue;
			}

			$val = $values[$key];

			if ($key === $prefix) {
				$key = $defaultKey;
			} else {
				$key = trim(substr($key, strlen($prefix)), '_');
			}

			$return[$key] = $val;
		}
		ksort($return, SORT_STRING | SORT_FLAG_CASE);

		return $return;
	}

/**
 * parseCache
 *
 * @param array $defaults
 * @return array
 */
	public static function parseCache($defaults = [], $replacements = []) {
		return static::parsePrefix('CACHE_URL', $defaults, $replacements, [__CLASS__, '_parseCache']);
	}

/**
 * parseDb
 *
 * @param array $defaults
 * @param array $replacements
 * @return array
 */
	public static function parseDb($defaults = [], $replacements = []) {
		return static::parsePrefix('DATABASE_URL', $defaults, $replacements, [__CLASS__, '_parseDb']);
	}

/**
 * parseLogs
 *
 * @param array $defaults
 * @param array $replacements
 * @return array
 */
	public static function parseLogs($defaults = [], $replacements = []) {
		return static::parsePrefix('LOG_URL', $defaults, $replacements, [__CLASS__, '_parseLog'], 'debug');
	}

/**
 * parsePrefix
 *
 * @param string $prefix
 * @param array $defaults
 * @param array $replacements
 * @param callable $callback
 * @param string $prefixDefault
 * @return array
 */
	public static function parsePrefix($prefix, $defaults, $replacements, $callback = null, $prefixDefault = 'default') {
		$data = static::allByPrefix($prefix, $prefixDefault);
		if (!$data) {
			return false;
		}

		if (!$callback) {
			$callback = [__CLASS__, '_parse'];
		}

		$return = [];

		foreach($data as $key => $url) {
			$config = static::parseUrl($url);
			if (!$config) {
				continue;
			}

			$key = strtolower($key);
			$return += $callback($key, $config, $defaults);
		}

		return static::_replace($return, $replacements);
	}

/**
 * parseUrl
 *
 * Parse a url and merge with any extra get arguments defined
 *
 * @param string $string
 * @return array
 */
	public static function parseUrl($string) {
		$url = parse_url($string);
		if (!$url) {
			debug ($string);
			return false;
		}

		if (isset($url['query'])) {
			$extra = [];
			parse_str($url['query'], $extra);
			unset($url['query']);
			$url += $extra;
		}

		return $url;
	}

/**
 * get a value out of an array if it exists
 *
 * @param array $data
 * @param string $key
 * @return mixed
 */
	protected static function _get($data, $key) {
		if (isset($data[$key])) {
			return $data[$key];
		}

		return null;
	}

/**
 * Generic/noop handler
 *
 * @param mixed $key
 * @param mixed $config
 * @param mixed $defaults
 * @return array
 */
	protected static function _parse($key, $config, $defaults) {
		$name = isset($config['name']) ? $config['name'] : trim($key, '_');

		$config += $defaults;

		return [$name => $config];
	}

/**
 * Recursively perform string replacements on array values
 *
 * @param array $data
 * @param array $replacements
 * @return array
 */
	protected static function _replace($data, $replacements) {
		if (!$replacements) {
			return $data;
		}

		foreach($data as &$value) {
			$value = str_replace(array_keys($replacements), array_values($replacements), $value);
			if (is_array($value)) {
				$value = static::_replace($value, $replacements);
			}
		}

		return $data;
	}

}
