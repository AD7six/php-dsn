<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class DbDsn extends Dsn
{

    protected $defaultOptions = [
        'keyMap' => [
            'engine' => 'datasource',
            'user' => 'login',
            'pass' => 'password'
        ]
    ];

    public static function parse($url, $options = [])
    {
        $inst = new DbDsn($url, $options);
        return $inst->toArray();
    }

    public function getDatasource()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }
        return 'Database/' . ucfirst($this->dsn->engine);
    }

    public function setDatasource($value)
    {
        $this->dsn->engine = str_replace('Database/', '', $value);
    }
}
