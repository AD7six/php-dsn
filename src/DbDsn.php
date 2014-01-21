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
 * _keyMap
 *
 * The path is the database name
 *
 * @var array
 */
	protected $_keyMap = [
		'scheme' => 'engine',
		'path' => 'database'
	];

/**
 * getEngine
 *
 * @return string
 */
	public function getScheme() {
		return $this->getEngine();
	}

/**
 * setScheme;
 *
 * @param string $db
 * @return void
 */
	public function setScheme($db) {
		$this->engine = $db;
	}

/**
 * getDatabase
 *
 * @return string
 */
	public function getDatabase() {
		return $this->_url['database'];
	}

/**
 * setDatabase
 *
 * @param string $path
 * @return void
 */
	public function setDatabase($db) {
		$this->_url['database'] = $db;
	}

/**
 * getPath
 *
 * @return string
 */
	public function getPath() {
		if ($this->_databaseIsFile) {
			return $this->database;
		}
		return '/' . $this->getDatabase();
	}

/**
 * setPath
 *
 * @param string $path
 * @return void
 */
	public function setPath($path) {
		if (!$this->_databaseIsFile) {
			$path = ltrim($path, '/');
		}
		$this->setDatabase($path);
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
		$engine = null;

		if ($this->_databaseIsFile) {
			$engine = substr($string, 0, strpos($string, ':'));
			$string = 'file' . substr($string, strlen($engine));
		}

		parent::parseUrl($string);

		if ($engine !== null) {
			$this->setEngine($engine);
		}
	}

/**
 * return this instance as a dsn url string
 *
 * @return string
 */
	public function toUrl() {
		$url = $this->_url;
		unset($url['engine'], $url['database']);
		$url['scheme'] = $this->getScheme();
		$url['path'] = $this->getPath();

		return $this->_toUrl($url);
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

}
