<?php

namespace AD7six\Dsn;

class Dsn {

	protected $_url = [];

	protected $_defaultPort;

	protected $_dsnKeys = [
		'scheme' => null,
		'host' => null,
		'port' => null,
		'user' => null,
		'pass' => null,
		'path' => null
	];

	public function __construct($string = '', $keyMap = [], $defaultPort = null) {
		$this->_defaultPort = $defaultPort;
		$this->_keyMap = $keyMap;
		$this->parseUrl($string);
	}

/**
 * parseUrl
 *
 * Parse a url and merge with any extra get arguments defined
 *
 * @param string $string
 * @return void
 */
	public function parseUrl($string) {
		$this->_url = [];

		$url = parse_url($string);
		if (!$url || array_keys($url) === ['path']) {
			return false;
		}

		if ($this->_defaultPort && empty($url['port'])) {
			$url['port'] = $this->_defaultPort;
		}

		if (isset($url['query'])) {
			$extra = [];
			parse_str($url['query'], $extra);
			unset($url['query']);
			$url += $extra;
		}

		$url = array_merge($this->_dsnKeys, $url);

		$this->_url = $url;
	}

/**
 * toArray
 *
 * Honor the key map, keeping the original key order (don't reorder keys whilst
 * translating them
 *
 * @return array
 */
	public function toArray() {
		$url = $this->_url;

		$return = [];
		foreach($url as $key => $val) {
			if (isset($this->_keyMap[$key])) {
				$key = $this->_keyMap[$key];
			}
			$return[$key] = $val;
		}

		return $return;
	}

/**
 * __get
 *
 * Allow accessing any of the parsed parts of a dsn, honoring the keymap
 *
 * @param mixed $name
 * @return void
 */
	public function __get($name) {
		if ($key = array_search($name, $this->_keyMap)) {
			$name = $key;
		} elseif (isset($this->_keyMap[$name])) {
			return null;
		}

		if (array_key_exists($name, $this->_url)) {
			return $this->_url[$name];
		}

		return null;
	}

	public function __toString() {
		$url = array_intersect_key($this->_url, $this->_dsnKeys);

		$return = $url['scheme'] . '://';
		if (!empty($url['user'])) {
			$return .= $url['user'];
			if (!empty($url['pass'])) {
				$return .= ':' . $url['pass'];
			}
			$return .= '@';
		}
		$return .= $url['host'];
		if (!empty($url['port'])) {
			$return .= ':' . $url['port'];
		}
		$return .= $url['path'];

		$query = array_diff_key($this->_url, $this->_dsnKeys);
		if ($query) {
			foreach($query as $key => &$value) {
				$value = "$key=$value";
			}
			$return .= '?' . implode($query, '&');
		}

		return $return;
	}

/**
 * get a value out of an array if it exists
 *
 * @param array $data
 * @param string $key
 * @return mixed
 */
	protected function _get($data, $key) {
		if (isset($data[$key])) {
			return $data[$key];
		}

		return null;
	}

/**
 * Recursively perform string replacements on array values
 *
 * @param array $data
 * @param array $replacements
 * @return array
 */
	protected function _replace($data, $replacements) {
		if (!$replacements) {
			return $data;
		}

		foreach($data as &$value) {
			$value = str_replace(array_keys($replacements), array_values($replacements), $value);
			if (is_array($value)) {
				$value = $this->_replace($value, $replacements);
			}
		}

		return $data;
	}

}
