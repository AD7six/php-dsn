<?php

namespace AD7six\Dsn;

/**
 * Dsn
 *
 */
class Dsn
{

/**
 * Array of scheme => class files
 *
 * @var array
 */
    protected static $schemeMap = [
        'cassandra' => '\AD7six\Dsn\Db\CassandraDsn',
        'couch' => '\AD7six\Dsn\Db\CouchdbDsn',
        'couchdb' => '\AD7six\Dsn\Db\CouchdbDsn',
        'db' => '\AD7six\Dsn\DbDsn',
        'maria' => '\AD7six\Dsn\Db\MysqlDsn',
        'mariadb' => '\AD7six\Dsn\Db\MysqlDsn',
        'mongo' => '\AD7six\Dsn\Db\MongodbDsn',
        'mongodb' => '\AD7six\Dsn\Db\MongodbDsn',
        'mssql' => '\AD7six\Dsn\Db\SqlserverDsn',
        'mysql' => '\AD7six\Dsn\Db\MysqlDsn',
        'oracle' => '\AD7six\Dsn\Db\OracleDsn',
        'pg' => '\AD7six\Dsn\Db\PostgresDsn',
        'pgsql' => '\AD7six\Dsn\Db\PostgresDsn',
        'postgres' => '\AD7six\Dsn\Db\PostgresDsn',
        'sqlite' => '\AD7six\Dsn\Db\SqliteDsn',
        'sqlite3' => '\AD7six\Dsn\Db\SqliteDsn',
        'sqlserver' => '\AD7six\Dsn\Db\SqlserverDsn',
    ];

/**
 * parsed url as an associative array
 *
 * @var array
 */
    protected $url = [];

/**
 * The default port used in connections
 *
 * This is overriden in subclasses
 *
 * @var int
 */
    protected $defaultPort;

/**
 * The keys present in a dsn
 *
 * When regenerating a dsn string, all other keys are converted to get arguments
 *
 * @var array
 */
    protected $uriKeys = [
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
    public static function parse($url)
    {
        $scheme = substr($url, 0, strpos($url, ':'));
        if (strpos($scheme, '+')) {
            $scheme = substr($scheme, 0, strpos($scheme, '+'));
        }

        if (!$scheme) {
            throw new \Exception(sprintf('The url \'%s\' could not be parsed', $url));
        }

        if (isset(static::$schemeMap[$scheme])) {
            $className = static::$schemeMap[$scheme];
        } else {
            $className  = static::currentNamespace() . '\\' . ucfirst($scheme) . 'Dsn';
            if (!class_exists($className)) {
                $className  = static::currentClass();
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
    public static function map($scheme = null, $class = null)
    {
        if (is_array($scheme)) {
            foreach ($scheme as $s => $class) {
                static::map($s, $class);
            }
            return static::$schemeMap;
        }

        if ($scheme === null) {
            return static::$schemeMap;
        }

        if ($class === null) {
            return isset(static::$schemeMap[$scheme]) ? static::$schemeMap[$scheme] : null;
        }

        if ($class === false) {
            unset($schemeMap[$scheme]);
            return;
        }

        return static::$schemeMap[$scheme] = $class;
    }

/**
 * LSB wrapper
 *
 * @return string
 */
    protected static function currentClass()
    {
        return __CLASS__;
    }

/**
 * LSB wrapper
 *
 * @return string
 */
    protected static function currentNamespace()
    {
        return __NAMESPACE__;
    }

/**
 * Create a new instance, parse the url if passed
 *
 * @param string $url
 * @return void
 */
    public function __construct($url = '')
    {
        $this->parseUrl($url);
    }

/**
 * Get or set the default port
 *
 * @param int $port
 * @return int
 */
    public function defaultPort($port = null)
    {
        if (!is_null($port)) {
            $this->defaultPort = (int)$port;
            if ($this->url['port'] === null) {
                $this->url['port'] = $this->defaultPort;
            }
        }

        return $this->defaultPort;
    }

/**
 * parseUrl
 *
 * Parse a url and merge with any extra get arguments defined
 *
 * @param string $string
 * @return void
 */
    public function parseUrl($string)
    {
        $this->url = [];

        $url = parse_url($string);
        if (!$url || !isset($url['scheme'])) {
            throw new \Exception(sprintf('The url \'%s\' could not be parsed', $string));
        }

        $this->parseUrlArray($url);
    }

/**
 * Worker function for parseUrl
 *
 * Take the passed array, and using getters update the instance
 *
 * @param array $url
 * @return void
 */
    protected function parseUrlArray($url)
    {
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

        $url = array_merge($this->uriKeys, $url);

        foreach (['host', 'user', 'pass'] as $key) {
            if (!isset($url[$key])) {
                continue;
            }

            $url[$key] = urldecode($url[$key]);
        }

        foreach ($url as $key => $val) {
            $setter = 'set' . ucfirst($key);
            $this->$setter($val);
        }
    }

/**
 * toArray
 *
 * Return this instance as an associative array using getter methods if they exist
 *
 * @return array
 */
    public function toArray()
    {
        $url = $this->url;

        $return = [];
        foreach (array_keys($url) as $key) {
            $getter = 'get' . ucfirst($key);
            $val = $this->$getter();

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
    public function toUrl()
    {
        return $this->toUrlArray($this->url);
    }

/**
 * Worker function for toUrl - does not rely on instance state
 *
 * @param array $data
 * @return string
 */
    protected function toUrlArray($data)
    {
        $url = array_intersect_key($data, $this->uriKeys);

        foreach (['host', 'user', 'pass'] as $key) {
            if (!isset($url[$key])) {
                continue;
            }

            $url[$key] = urlencode($url[$key]);
        }

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

        $query = array_diff_key($data, $this->uriKeys);
        if ($query) {
            foreach ($query as $key => &$value) {
                if (is_array($value)) {
                    $intermediate = [];
                    foreach ($value as $k => $v) {
                        $v = urlencode($v);
                        $intermediate[] = "{$key}[$k]=$v";
                    }
                    $value = implode($intermediate, '&');

                    continue;
                }
                $value = "$key=$value";
            }
            $return .= '?' . implode($query, '&');
        }

        return $return;
    }

/**
 * Allow accessing any of the parsed parts of a dsn as an object property
 *
 * @param mixed $key
 * @return mixed
 */
    public function __get($key)
    {
        $getter = 'get' . ucfirst($key);
        return $this->$getter();
    }

/**
 * Allow setting any of the parsed parts of a dsn by updating the "public" property
 *
 * @param string $key
 * @param string $value
 * @return void
 */
    public function __set($key, $value)
    {
        $setter = 'set' . ucfirst($key);
        return $this->$setter($value);
    }

/**
 * __toString
 *
 * @return string
 */
    public function __toString()
    {
        return $this->toUrl();
    }

/**
 * Handle dynamic/missing getters and setters
 *
 * @param string $method
 * @param array $args
 * @return void
 */
    public function __call($method, $args)
    {
        $getSet = substr($method, 0, 3);
        if ($getSet === 'get' || $getSet === 'set') {
            $key = lcfirst(substr($method, 3));

            if ($getSet === 'get') {
                if (array_key_exists($key, $this->url)) {
                    return $this->url[$key];
                }
                return null;
            }

            $this->url[$key] = $args[0];
            return;
        }

        throw new \BadMethodCallException(sprintf('Method %s not implemented', $method));
    }
}
