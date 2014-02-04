<?php

namespace AD7six\Dsn\Wrapper;

class DynamicDsn extends Dsn
{

    protected $getters = [];

    protected $setters = [];

    public function __construct($url = '', $options = [])
    {
        $opts = [
            'getters',
            'setters'
        ];
        foreach ($opts as $key) {
            if (!empty($options[$key])) {
                $this->$key($options[$key]);
            }
        }

        parent::__construct($url, $options);
    }

    public function getters($getters = null)
    {
        if (!$getters) {
            return $this->getters;
        }

        foreach ($getters as $key => $callable) {
            $this->addGetter($key, $callable);
        }
    }

    public function setters($setters = null)
    {
        if (!$setters) {
            return $this->setters;
        }

        foreach ($setters as $key => $callable) {
            $this->addSetter($key, $callable);
        }
    }

    public function addGetter($key, callable $callable)
    {
        $this->getters[$key] = $callable;
    }

    public function addSetter($key, callable $callable)
    {
        $this->setters[$key] = $callable;
    }

/**
 * Proxy getting data from the dsn instance
 *
 * @param mixed $key
 * @return mixed
 */
    public function __get($key)
    {
        if (isset($this->getters[$key])) {
            return $this->getters[$key]($this->dsn->$key, $this->dsn);
        }

        return parent::__get($key);
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
        if (isset($this->setters[$key])) {
            $return = $this->setters[$key]($value, $key, $this->dsn);
            if ($return === null) {
                return;
            }
            $value = $return;
        }

        if ($actualKey = array_search($key, $this->keyMap)) {
            $key = $actualKey;
        }

        $this->dsn->$key = $value;
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

            if ($getSet === 'get') {
                if (isset($this->getters[$key])) {
                    return $this->getters[$key]($this->dsn->$key, $this->dsn);
                }
            } else {
                if (isset($this->setters[$key])) {
                    $return = $this->setters[$key]($value, $key, $this->dsn);
                    if ($return !== null) {
                        $this->dsn->$key = $return;
                    }
                    return;
                }
            }
        }

        return parent::__call($method, $args);
    }
}
