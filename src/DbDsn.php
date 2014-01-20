<?php

namespace AD7six\Dsn;

/**
 * DbDsn
 *
 */
class DbDsn extends Dsn {

/**
 * _defaultPort
 *
 * Overriden in subclasses
 *
 * @var int
 */
	protected $_defaultPort;

/**
 * _keyMap
 *
 * The path is the database name
 *
 * @var array
 */
	protected $_keyMap = [
		'path' => 'database'
	];

/**
 * keyMap
 *
 * make sure that the path is translated too, but not if it's already redefined
 *
 * @param mixed $keyMap
 * @return mixed
 */
	public function keyMap($keyMap = null) {
		if ($keyMap) {
			$keyMap += ['path' => 'database'];
		}
		return parent::keyMap($keyMap);
	}

/**
 * parseUrl
 *
 * The database (path) will have a leading slash - strip it off
 *
 * @param string $string
 * @return array
 */
	public function parseUrl($string) {
		$return = parent::parseUrl($string);

		if (isset($this->_url['path'])) {
			$this->_url['path'] = ltrim($this->_url['path'], '/');
		}

		return $return;
	}

/**
 * _toUrl
 *
 * Re-prefix the database (path) with a leading slash
 *
 * @param mixed $data
 * @return string
 */
	protected function _toUrl($data) {
		if (isset($data['path'])) {
			$data['path'] = '/' . $data['path'];
		}
		return parent::_toUrl($data);
	}
}
