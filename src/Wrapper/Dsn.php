<?php

namespace AD7six\Dsn\Wrapper;

use AD7six\Dsn\Dsn as DsnInstance;

class Dsn
{

/**
 * Array of scheme => adapters
 *
 * @var array
 */
    protected static $adapterMap = [];

/**
 * Array of keys which must be present when parsed as an array
 *
 * @var array
 */
    protected static $mandatoryKeys = [
        'adapter'
    ];

/**
 * defaultOptions
 *
 * @var array
 */
    protected $defaultOptions = [];

/**
 * keyMap
 *
 * Internal storage of key name translations
 *
 * @var array
 */
    protected $keyMap = [];

/**
 * Actual dsn instance
 *
 * @var AD7six\Dsn\Dsn
 */
    protected $dsn;

/**
 * replacements
 *
 * array of strings which are replaced in parsed dsns
 *
 * @var array
 */
    protected $replacements = [];

/**
 * Get an environment variable
 *
 * Query $_SERVER, $_ENV and getenv() in that order
 *
 * @param string $key
 * @return mixed
 */
    public function env($key = null)
    {
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            return $_ENV[$key];
        } elseif (getenv($key) !== false) {
            return getenv($key);
        }
        return null;
    }

/**
 * Read or change the adapter map
 *
 * For example:
 *
 * $true = Dsn::map('foo', 'UseThisAdapter');
 * $dsn = Dsn::parse('foo://....');
 * $dsn->adapter === 'UseThisAdapter';
 *
 * $array = ['this' => 'That\Class', 'other' => 'Other\Class', ...];
 * $fullMap = Dsn::map($array);
 * $dsn = Dsn::parse('this://....');
 * $dsn->adapter === 'That\Class';
 * $dsn = Dsn::parse('other://....');
 * $dsn->adapter === 'Other\Class';
 *
 * @param mixed $adapter
 * @param string $class
 * @return mixed
 */
    public static function map($scheme = null, $adapter = null)
    {
        if (is_array($scheme)) {
            foreach ($scheme as $s => $adapter) {
                static::map($s, $adapter);
            }
            return static::$adapterMap;
        }

        if ($scheme === null) {
            return static::$adapterMap;
        }

        if ($adapter === null) {
            return isset(static::$adapterMap[$scheme]) ? static::$adapterMap[$scheme] : null;
        }

        if ($adapter === false) {
            unset($adapterMap[$scheme]);
            return;
        }

        return static::$adapterMap[$scheme] = $adapter;
    }

    public static function parse($url, $options = [])
    {
        return new Dsn($url, $options);
    }

    public function __construct($url = '', $options = [])
    {
        $this->dsn = DsnInstance::parse($url);

        $options = $this->mergeDefaultOptions($options);
        $opts = [
            'keyMap',
            'replacements',
        ];
        foreach ($opts as $key) {
            if (!empty($options[$key])) {
                $this->$key($options[$key]);
            }
        }
    }

    protected function getDefaultOptions()
    {
        if (!isset($this->defaultOptions['replacements']['APP_NAME'])) {
            $this->defaultOptions['replacements']['APP_NAME'] = $this->env('APP_NAME');
        }

        return $this->defaultOptions;
    }

    protected function mergeDefaultOptions($options = [])
    {
        $defaults = $this->getDefaultOptions();

        foreach (array_keys($defaults) as $key) {
            if (!isset($options[$key])) {
                $options[$key] = [];
            }
            $options[$key] += $defaults[$key];
        }

        return $options;
    }

    public function getAdapter()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter !== null) {
            return $adapter;
        }

        $scheme = $this->dsn->scheme;

        if (isset(static::$adapterMap[$scheme])) {
            return static::$adapterMap[$scheme];
        }

        return null;
    }

    public function getDsn()
    {
        return $this->dsn;
    }

    public function toArray()
    {
        $raw = $this->dsn->toArray();
        $allKeys = array_unique(array_merge(static::$mandatoryKeys, array_keys($raw)));
        $return = [];

        foreach ($allKeys as $key) {
            if (isset($this->keyMap[$key])) {
                $key = $this->keyMap[$key];
                if (!$key) {
                    continue;
                }
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
    public function keyMap($keyMap = null)
    {
        if (!is_null($keyMap)) {
            $this->keyMap = $keyMap;
        }

        return $this->keyMap;
    }

/**
 * Get or set replacements
 *
 * @param mixed $replacements
 * @return array
 */
    public function replacements($replacements = null)
    {
        if (!is_null($replacements)) {
            $this->replacements = $replacements;
        }

        return $this->replacements;
    }

/**
 * Recursively perform string replacements on array values
 *
 * @param array $data
 * @param array $replacements
 * @return array
 */
    protected function replace($data, $replacements = null)
    {
        if (!is_string($data)) {
            return $data;
        }

        if (!$replacements) {
            $replacements = $this->replacements();
            if (!$replacements) {
                return $data;
            }
        }

        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                $value = $this->replace($value, $replacements);
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
    public function __get($key)
    {
        $getter = 'get' . ucfirst($key);
        $val = $this->$getter();
        return $this->replace($val);
    }

/**
 * Proxy setting data from the dsn instance
 *
 * @param string $key
 * @param string $value
 * @return void
 */
    public function __set($key, $value)
    {
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
    public function __call($method, $args)
    {
        $getSet = substr($method, 0, 3);
        if ($getSet === 'get' || $getSet === 'set') {
            $key = lcfirst(substr($method, 3));

            if ($aliased = array_search($key, $this->keyMap)) {
                if (!$aliased) {
                    return null;
                }
                $method = $getSet . ucfirst($aliased);
            }
        }

        return call_user_func_array([$this->dsn, $method], $args);
    }
}
