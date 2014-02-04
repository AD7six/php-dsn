<?php

namespace AD7six\Dsn\Wrapper;

use AD7six\Dsn\Dsn as DsnInstance;

class Dsn
{

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

    public function getDsn()
    {
        return $this->dsn;
    }

    public function toArray()
    {
        $raw = $this->dsn->toArray();
        $allKeys = array_keys($raw);
        $return = [];

        foreach ($allKeys as $key) {
            if (isset($this->keyMap[$key])) {
                $key = $this->keyMap[$key];
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
        if (!$replacements) {
            $replacements = $this->replacements;
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
        return call_user_func_array([$this->dsn, $method], $args);
    }
}
