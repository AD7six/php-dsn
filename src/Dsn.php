<?php

namespace AD7six\Dsn;

/**
 * Dsn
 *
 */
class Dsn {

/**
 * parsed url as an associative array. the keys are the "native" keys
 *
 * @var array
 */
	protected $_url = [];

/**
 * The default port used in connections
 *
 * This is overriden in subclasses
 *
 * @var int
 */
	protected $_defaultPort;

/**
 * The mandatory keys present in a dsn
 *
 * When regenerating a dsn string, all other keys are converted to get arguments
 *
 * @var array
 */
	protected $_dsnKeys = [
		'scheme' => null,
		'host' => null,
		'port' => null,
		'user' => null,
		'pass' => null,
		'path' => null
	];

/**
 * _keyMap
 *
 * Internal storage of key name translations
 *
 * @var array
 */
	protected $_keyMap = [];

/**
 * parse a dsn string into a dsn instance
 *
 * If a specific dsn class is available an instance of that class is returned
 *
 * @param string $url
 * @param array $keyMap
 * @return mixed Dsn instance or false
 */
	public static function parse($url, $keyMap = []) {
		$scheme = substr($url, 0, strpos($url, ':'));
		if (!$scheme) {
			return false;
		}

		$className  = __NAMESPACE__ . '\\' . ucfirst($scheme) . 'Dsn';
		if (!class_exists($className)) {
			$className  = __CLASS__;
		}

		return new $className($url, $keyMap);
	}

/**
 * Create a new instance, parse the url if passed
 *
 * @param string $url
 * @param array $keyMap
 * @param int $defaultPort
 * @return void
 */
	public function __construct($url = '', $keyMap = [], $defaultPort = null) {
		if ($defaultPort) {
			$this->_defaultPort = $defaultPort;
		}
		if ($keyMap) {
			$this->keyMap($keyMap);
		}
		$this->parseUrl($url);
	}

/**
 * Set or get the default port
 *
 * @param int $port
 * @return int
 */
	public function defaultPort($port = null) {
		if (!is_null($port)) {
			$this->_defaultPort = $port;
		}

		return $this->_defaultPort;
	}

/**
 * Set or get the key map
 *
 * The key map permits translating the parsed array keys
 *
 * @param mixed $keyMap
 * @return array
 */
	public function keyMap($keyMap = null) {
		if (!is_null($keyMap)) {
			$this->_keyMap = $keyMap;
		}

		return $this->_keyMap;
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
 * return this instance as a dsn url string
 *
 * @return string
 */
	public function toUrl() {
		return $this->_toUrl($this->_url);
	}

/**
 * _toUrl
 *
 * @param mixed $data
 * @return void
 */
	protected function _toUrl($data) {
		$url = array_intersect_key($data, $this->_dsnKeys);

		$return = $url['scheme'] . '://';
		if (!empty($url['user'])) {
			$return .= $url['user'];
			if (!empty($url['pass'])) {
				$return .= ':' . $url['pass'];
			}
			$return .= '@';
		}
		$return .= $url['host'];
		if (!empty($url['port']) && $url['port'] != $this->_defaultPort) {
			$return .= ':' . $url['port'];
		}
		$return .= $url['path'];

		$query = array_diff_key($data, $this->_dsnKeys);
		if ($query) {
			foreach($query as $key => &$value) {
				$value = "$key=$value";
			}
			$return .= '?' . implode($query, '&');
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

/**
 * Allow setting any of the parsed parts of a dsn, honoring the keymap
 *
 * @param string $name
 * @param mixed $value
 * @return void
 */
	public function __set($name, $value) {
		if ($key = array_search($name, $this->_keyMap)) {
			$name = $key;
		} elseif (isset($this->_keyMap[$name])) {
			return;
		}

		$this->_url[$name] = $value;
	}

/**
 * __toString
 *
 * @return string
 */
	public function __toString() {
		return $this->toUrl();
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
