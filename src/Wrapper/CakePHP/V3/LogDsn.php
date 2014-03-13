<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V3;

use AD7six\Dsn\Wrapper\Dsn;

class LogDsn extends Dsn
{

    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'className'
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

    public function getClassName()
    {
        $adapter = $this->getAdapter();

        if ($adapter) {
            return $adapter;
        }

        $scheme = $this->dsn->scheme;

        return 'Cake\Log\Engine\\' . ucfirst($scheme) . 'Log';
    }

    public function getSerialize()
    {
        $return = $this->dsn->serialize;

        if ($return === null) {
            return;
        }
        return (int) $return;
    }

    public function getLevels()
    {
        $return = $this->dsn->levels;

        if ($return === null) {
            return;
        }
        return explode(',', $return);
    }
}
