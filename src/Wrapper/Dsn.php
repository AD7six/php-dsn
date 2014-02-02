<?php

namespace AD7six\Dsn\Wrapper;

use AD7six\Dsn\Dsn as DsnInstance;

class Dsn {

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

	public static function parse($url, $options = []) {
		return new Dsn($url, $options);
	}

	public function __construct($url = '', $options = []) {
		$this->_dsn = DsnInstance::parse($url);

		$options = $this->_mergeDefaultOptions($options);
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

	protected function _getDefaultOptions() {
		return $this->_defaultOptions;
	}

	protected function _mergeDefaultOptions($options = []) {
		$defaults = $this->_getDefaultOptions();

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
			return $data;
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
		$getter = 'get' . ucfirst($key);
		$val = $this->$getter();
		return $this->_replace($val);
	}

/**
 * Proxy setting data from the dsn instance
 *
 * @param string $key
 * @param string $value
 * @return void
 */
	public function __set($key, $value) {
		$setter = 'set' . ucfirst($key);
		$this->$setter($value);
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
