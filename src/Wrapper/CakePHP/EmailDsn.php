<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class EmailDsn extends Dsn
{

    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'transport',
        ]
    ];

    public static function parse($url, $options = [])
    {
        $inst = new EmailDsn($url, $options);
        return $inst->toArray();
    }

    public function getTransport()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }
        return ucfirst($this->dsn->scheme);
    }

    public function setTransport($value)
    {
        $this->dsn->scheme = lcfirst($value);
    }
}
