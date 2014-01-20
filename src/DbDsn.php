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
 * pathIsPath
 *
 * The parsed url will have a path element which normally is _not_ a path; it's a database name
 * In the case it is a path (sqlite) this property will prevent mishandling of the path as
 * a database name
 *
 * @var bool
 */
	protected $_pathIsPath = false;

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
		if ($this->_pathIsPath) {
			$scheme = substr($string, 0, strpos($string, ':'));
			$string = 'file' . substr($string, strlen($scheme));
		}

		$return = parent::parseUrl($string);

		if ($this->_pathIsPath) {
			$this->_url['scheme'] = $scheme;
		} elseif (isset($this->_url['path'])) {
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
		if (!$this->_pathIsPath && isset($data['path'])) {
			$data['path'] = '/' . $data['path'];
		}
		return parent::_toUrl($data);
	}
}
