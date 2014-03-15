<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V2;

use AD7six\Dsn\Wrapper\Dsn;

/**
 * LogDsn
 *
 */
class LogDsn extends Dsn
{

/**
 * defaultOptions
 *
 * @var array
 */
    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'engine'
        ],
        'replacements' => [
            'APP_NAME' => APP_NAME,
            '/LOGS/' => LOGS
        ]
    ];

/**
 * getEngine
 *
 * Return the adapter if there is one, else return the scheme
 *
 * @return string
 */
    public function getEngine()
    {
        $adapter = $this->getAdapter();

        if ($adapter) {
            return $adapter;
        }

        return ucfirst($this->dsn->scheme);
    }

/**
 * getProbability
 *
 * @return int
 */
    public function getProbability()
    {
        $return = $this->dsn->probability;

        if ($return === null) {
            return;
        }
        return (int) $return;
    }

/**
 * getSerialize
 *
 * @return bool
 */
    public function getSerialize()
    {
        $return = $this->dsn->serialize;

        if ($return === null) {
            return;
        }
        return (bool) $return;
    }

/**
 * getTypes
 *
 * If it's defined, return as an array
 *
 * @return array
 */
    public function getTypes()
    {
        $return = $this->dsn->types;

        if ($return === null) {
            return;
        }
        return explode(',', $return);
    }

/**
 * parse a url as a log dsn
 *
 * @param string $url
 * @param array $options
 * @return array
 */
    public static function parse($url, $options = [])
    {
        $inst = new LogDsn($url, $options);

        return $inst->toArray();
    }
}
