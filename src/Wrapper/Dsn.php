<?php

namespace AD7six\Dsn\Wrapper;

use AD7six\Dsn\Dsn as DsnInstance;

class Dsn implements \ArrayAccess {

/**
 * _defaultOptions
 *
 * @var array
 */
	protected $_defaultOptions = [
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
 * Actual dsn instance
 *
 * @var AD7six\Dsn\Dsn
 */
	protected $_dsn;

/**
 * _replacements
 *
 * array of strings which are replaced in parsed dsns
 *
 * @var array
 */
	protected $_replacements = [];

	public function __construct($url = '', $options = []) {
		$this->_dsn = DsnInstance::parse($url);

		$opts = [
			'keyMap',
			'replacements',
		];
		foreach($opts as $key) {
			if (!empty($options[$key])) {
				$this->$key($options[$key]);
			}
		}
	}

	public static function parse($url, $options = []) {
		$options = static::_mergeDefaultOptions($options);
		$inst = new Dsn($url, $options);
		return $inst;
	}

	protected static function _getDefaultOptions() {
		return static::$_defaultOptions;
	}

	protected static function _mergeDefaultOptions($options = [], $defaults = null) {
		if ($defaults === null) {
			$defaults = static::$_getDefaultOptions();
		}

		foreach(array_keys($defaults) as $key) {
			if (!isset($options[$key])) {
				$options[$key] = [];
			}
			$options[$key] += $defaults[$key];
		}

		return $options;
	}

	public function getDsn() {
		return $this->_dsn;
	}

	public function toArray() {
		$raw = $this->_dsn->toArray();
		$allKeys = array_keys($raw);
		$return = [];

		foreach($allKeys as $key) {
			if (isset($this->_keyMap[$key])) {
				$key = $this->_keyMap[$key];
			}

			$val = $this->$key;
			if ($val !== null) {
				$return[$key] = $val;
			}

		}

		return $return;
	}

/**
 * Get or set the key map
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
 * Get or set replacements
 *
 * @param mixed $replacements
 * @return array
 */
	public function replacements($replacements = null) {
		if (!is_null($replacements)) {
			$this->_replacements = $replacements;
		}

		return $this->_replacements;
	}

	public function offsetExists($index) {
		return $this->$index !== null;
	}

	public function offsetGet($index) {
		return $this->$index;
	}

	public function offsetSet($index, $value) {
		return $this->$index = $value;
	}

	public function offsetUnset($index) {
		$this->$index = null;
	}

	public function count() {
		return count($this->toArray());
	}

/**
 * Recursively perform string replacements on array values
 *
 * @param array $data
 * @param array $replacements
 * @return array
 */
	protected function _replace($data, $replacements = null) {
		if (!$replacements) {
			$replacements = $this->_replacements;
			if (!$replacements) {
				return $data;
			}
		}

		if (is_array($data)) {
			foreach($data as $key => &$value) {
				$value = $this->_replace($value, $replacements);
			}
			return $data;;
		}

		return str_replace(array_keys($replacements), array_values($replacements), $data);
	}

/**
 * Proxy getting data from the dsn instance
 *
 * @param mixed $key
 * @return mixed
 */
	public function __get($key) {
		if ($actualKey = array_search($key, $this->_keyMap)) {
			$key = $actualKey;
		}

		return $this->_replace($this->_dsn->$key);
	}

/**
 * Proxy setting data from the dsn instance
 *
 * @param string $key
 * @param string $value
 * @return void
 */
	public function __set($key, $value) {
		if ($actualKey = array_search($key, $this->_keyMap)) {
			$key = $actualKey;
		}

		$this->_dsn->$key = $value;
	}

/**
 * Proxy method calls to the dsn instance
 *
 * @param string $method
 * @param array $args
 * @return void
 */
	public function __call($method, $args) {
		return call_user_func_array([$this->_dsn, $method], $args);
	}
}
