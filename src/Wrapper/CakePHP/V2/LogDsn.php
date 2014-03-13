<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V2;

use AD7six\Dsn\Wrapper\Dsn;

class LogDsn extends Dsn
{

    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'engine'
        ],
        'replacements' => [
            '/LOGS/' => LOGS
        ]
    ];

    public static function parse($url, $options = [])
    {
        $inst = new LogDsn($url, $options);

        return $inst->toArray();
    }

    public function getGroups()
    {
        $return = $this->dsn->groups;

        if ($return === null) {
            return;
        }
        return explode(',', $return);
    }

    public function getLock()
    {
        $return = $this->dsn->lock;

        if ($return === null) {
            return;
        }
        return (int) $return;
    }

    public function getProbability()
    {
        $return = $this->dsn->probability;

        if ($return === null) {
            return;
        }
        return (int) $return;
    }

    public function getEngine()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }
        return ucfirst($this->dsn->scheme);
    }

    public function getSerialize()
    {
        $return = $this->dsn->serialize;

        if ($return === null) {
            return;
        }
        return (int) $return;
    }

    public function getTypes()
    {
        $return = $this->dsn->types;

        if ($return === null) {
            return;
        }
        return explode(',', $return);
    }
}
