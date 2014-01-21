<?php

namespace AD7six\Dsn;

/**
 * DbDsn
 *
 */
class DbDsn extends Dsn {

/**
 * is the database key a name or a file?
 *
 * The database key is a renamed path key. As a parsed path key, it's always a path
 * But it's _normally_ not a path, rather the name of the database to connect to.
 *
 * @var bool
 */
	protected $_databaseIsFile = false;

/**
 * getDatabase
 *
 * @return string
 */
	public function getDatabase() {
		if ($this->_databaseIsFile) {
			return $this->_url['database'];
		}
		return ltrim($this->_url['database'], '/');
	}

/**
 * setDatabase
 *
 * @param string $db
 * @return void
 */
	public function setDatabase($db) {
		if ($this->_databaseIsFile) {
			return $this->_url['database'] = $db;
		}
		$this->_url['database'] = '/' . $db;
	}

/**
 * parseUrl
 *
 * Handle the parent method only dealing with paths for the file scheme
 *
 * @param string $string
 * @return array
 */
	public function parseUrl($string) {
		$scheme = null;

		if ($this->_databaseIsFile) {
			$scheme = substr($string, 0, strpos($string, ':'));
			$string = 'file' . substr($string, strlen($scheme));
		}

		parent::parseUrl($string);

		if ($scheme !== null) {
			$this->_url['scheme'] = $scheme;
		}

		if (isset($this->_url['path'])) {
			$this->_url['database'] = $this->_url['path'];
			unset($this->_url['path']);
		}
	}

/**
 * LSB wrapper
 *
 * @return string
 */
	protected static function _class() {
		return __CLASS__;
	}

/**
 * LSB wrapper
 *
 * @return string
 */
	protected static function _namespace() {
		return __NAMESPACE__;
	}

/**
 * swap the database key for the path key for the parent implementation
 *
 * @param array $data
 * @return string
 */
	protected function _toUrl($data) {
		if (isset($data['database'])) {
			$data['path'] = $data['database'];
			unset($data['database']);
		}
		return parent::_toUrl($data);
	}

}
