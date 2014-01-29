<?php

namespace AD7six\Dsn\Wrapper;

use AD7six\Dsn\Dsn as DsnInstance;

class Dsn {

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

	protected $_getters = [];

	protected $_setters = [];

	public function __construct($url = '', $options = []) {
		$this->_dsn = DsnInstance::parse($url);

		$opts = [
			'keyMap',
			'replacements',
			'getters',
			'setters'
		];
		foreach($opts as $key) {
			if (!empty($options[$key])) {
				$this->$method($options[$key]);
			}
		}
	}

	public function getters($getters = null) {
		if (!$getters) {
			return $this->_getters;
		}

		foreach($getters as $key => $callable) {
			$this->addGetter($key, $callable);
		}
	}

	public function setters($setters = null) {
		if (!$setters) {
			return $this->_setters;
		}

		foreach($setters as $key => $callable) {
			$this->addSetter($key, $callable);
		}
	}

	public function addGetter($key, Callable $callable) {
		$this->_getters[$key] = $callable;
	}

	public function addSetter($key, Callable $callable) {
		$this->_setters[$key] = $callable;
	}

	public function getDsn() {
		return $this->_dsn;
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

/**
 * Proxy getting data from the dsn instance
 *
 * @param mixed $key
 * @return mixed
 */
	public function __get($key) {
		if (isset($this->_getters[$key])) {
			return $this->_getters[$key]($this->_dsn->$key, $this->_dsn);
		}

		return $this->_dsn->$key;
	}

/**
 * Proxy setting data from the dsn instance
 *
 * @param string $key
 * @param string $value
 * @return void
 */
	public function __set($key, $value) {
		if (isset($this->_setters[$key])) {
			$return = $this->_setters[$key]($value, $key, $this->_dsn);
			if ($return === null) {
				return;
			}
			$value = $return;
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
		$getSet = substr($method, 0, 3);
		if ($getSet === 'get' || $getSet === 'set') {
			$key = lcfirst(substr($method, 3));

			if ($getSet === 'get') {
				if (isset($this->_getters[$key])) {
					return $this->_getters[$key]($this->_dsn->$key, $this->_dsn);
				}
			} else {
				if (isset($this->_setters[$key])) {
					$return = $this->_setters[$key]($value, $key, $this->_dsn);
					if ($return !== null) {
						$this->_dsn->$key = $return;
					}
					return;
				}
			}
		}

		return call_user_func_array([$this->_dsn, $method], $args);
	}
}
