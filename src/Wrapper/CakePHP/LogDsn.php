<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

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

    public function getEngine()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }
        return ucfirst($this->dsn->scheme);
    }

    public function setEngine($value)
    {
        $this->dsn->scheme = lcfirst($value);
    }
}
