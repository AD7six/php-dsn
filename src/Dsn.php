<?php

namespace AD7six\Dsn;

/**
 * Dsn
 *
 */
class Dsn {

/**
 * Array of scheme => class files
 */
	protected static $_schemeMap = [
		'cassandra' => '\AD7six\Dsn\Db\CassandraDsn',
		'couchdb' => '\AD7six\Dsn\Db\CouchdbDsn',
		'db' => '\AD7six\Dsn\DbDsn',
		'mongo' => '\AD7six\Dsn\Db\MongodbDsn',
		'mysql' => '\AD7six\Dsn\Db\MysqlDsn',
		'postgres' => '\AD7six\Dsn\Db\PostgresDsn',
		'sqlite' => '\AD7six\Dsn\Db\SqliteDsn',
	];

/**
 * parsed url as an associative array
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
 * The keys present in a dsn
 *
 * When regenerating a dsn string, all other keys are converted to get arguments
 *
 * @var array
 */
	protected $_uriKeys = [
		'scheme' => null,
		'adapter' => null,
		'host' => null,
		'port' => null,
		'user' => null,
		'pass' => null,
		'path' => null
	];

/**
 * parse a dsn string into a dsn instance
 *
 * If a more specific dsn class is available an instance of that class is returned
 *
 * @param string $url
 * @return mixed Dsn instance or false
 */
	public static function parse($url) {
		$scheme = substr($url, 0, strpos($url, ':'));
		if (!$scheme) {
			return false;
		}

		if (isset(static::$_schemeMap[$scheme])) {
			$className = static::$_schemeMap[$scheme];
		} else {
			$className  = static::_namespace() . '\\' . ucfirst($scheme) . 'Dsn';
			if (!class_exists($className)) {
				$className  = static::_class();
			}
		}
		return new $className($url);
	}

/**
 * Read or change the scheme map
 *
 * For example:
 *
 * $true = Dsn::map('foo', '\My\Dsn\Class');
 * $classname = Dsn::map('foo');
 *
 * $true = Dsn::map('foo', false); // Remove foo from the map
 *
 * $fullMap = Dsn::map();
 * $false = Dsn::map('unknown');
 *
 * $array = ['this' => 'That\Class', 'other' => 'Other\Class', ...];
 * $fullMap = Dsn::map($array);
 *
 * @param mixed $scheme
 * @param string $class
 * @return mixed
 */
	public static function map($scheme = null, $class = null) {
		if (is_array($scheme)) {
			foreach($scheme as $s => $class) {
				static::map($s, $class);
			}
			return static::$_schemeMap;
		}

		if ($scheme === null) {
			return static::$_schemeMap;
		}

		if ($class === null) {
			return isset(static::$_schemeMap[$scheme]) ? static::$_schemeMap[$scheme] : null;
		}

		if ($class === false) {
			unset($_schemeMap[$scheme]);
			return;
		}

		return static::$_schemeMap[$scheme] = $class;
	}

/**
 * Create a new instance, parse the url if passed
 *
 * @param string $url
 * @return void
 */
	public function __construct($url = '') {
		$this->parseUrl($url);
	}

/**
 * Get or set the default port
 *
 * @param int $port
 * @return int
 */
	public function defaultPort($port = null) {
		if (!is_null($port)) {
			$this->_defaultPort = (int)$port;
			if ($this->_url['port'] === null) {
				$this->_url['port'] = $this->_defaultPort;
			}
		}

		return $this->_defaultPort;
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
			throw new \Exception(sprintf('The url \'%s\' could not be parsed', $string));
		}

		$this->_parseUrl($url);
	}

/**
 * Worker function for parseUrl
 *
 * Take the passed array, and using getters update the instance
 *
 * @param array $url
 * @return void
 */
	protected function _parseUrl($url) {
		if (strpos($url['scheme'], '+')) {
			list($url['scheme'], $url['adapter']) = explode('+', $url['scheme']);
		}

		$defaultPort = $this->defaultPort();
		if ($defaultPort && empty($url['port'])) {
			$url['port'] = $defaultPort;
		}

		if (isset($url['query'])) {
			$extra = [];
			parse_str($url['query'], $extra);
			unset($url['query']);
			$url += $extra;
		}

		$url = array_merge($this->_uriKeys, $url);

		foreach($url as $key => $val) {
			$this->$key = $val;
		}
	}

/**
 * toArray
 *
 * Return this instance as an associative array using getter methods if they exist
 *
 * @return array
 */
	public function toArray() {
		$url = $this->_url;

		$return = [];
		foreach(array_keys($url) as $key) {
			$val = $this->$key;

			if ($val !== null) {
				$return[$key] = $val;
			}
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
 * Worker function for toUrl - does not rely on instance state
 *
 * @param array $data
 * @return string
 */
	protected function _toUrl($data) {
		$url = array_intersect_key($data, $this->_uriKeys);

		if ($url['adapter']) {
			$return = $url['scheme'] . '+' . $url['adapter'] . '://';
		} else {
			$return = $url['scheme'] . '://';
		}

		if (!empty($url['user'])) {
			$return .= $url['user'];
			if (!empty($url['pass'])) {
				$return .= ':' . $url['pass'];
			}
			$return .= '@';
		}

		$return .= $url['host'];

		$defaultPort = $this->defaultPort();
		if (!empty($url['port']) && $url['port'] != $defaultPort) {
			$return .= ':' . $url['port'];
		}
		$return .= $url['path'];

		$query = array_diff_key($data, $this->_uriKeys);
		if ($query) {
			foreach($query as $key => &$value) {
				$value = "$key=$value";
			}
			$return .= '?' . implode($query, '&');
		}

		return $return;
	}

/**
 * Allow accessing any of the parsed parts of a dsn
 *
 * @param mixed $key
 * @return mixed
 */
	public function __get($key) {
		$getter = 'get' . ucfirst($key);
		return $this->$getter();
	}

/**
 * Allow setting any of the parsed parts of a dsn
 *
 * @param string $key
 * @param string $value
 * @return void
 */
	public function __set($key, $value) {
		$setter = 'set' . ucfirst($key);
		return $this->$setter($value);
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
 * Handle dynamic getters and setters
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
				if (array_key_exists($key, $this->_url)) {
					return $this->_url[$key];
				}
				return null;
			}

			$this->_url[$key] = $args[0];
			return;
		}

		throw new \BadMethodCallException(sprintf('Method %s not implemented', $method));
	}
}
