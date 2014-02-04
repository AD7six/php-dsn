<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class CacheDsn extends Dsn
{

    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'engine',
        ],
        'replacements' => [
            '/CACHE/' => CACHE
        ]
    ];

    public static function parse($url, $options = [])
    {
        $inst = new CacheDsn($url, $options);
        return $inst->toArray();
    }

    protected function getDefaultOptions()
    {
        if (!isset($this->defaultOptions['replacements']['DURATION'])) {
            $duration = \Configure::read('debug') ? '+10 seconds' : '+999 days';
            $this->defaultOptions['replacements']['DURATION'] = $duration;
        }

        if (!isset($this->defaultOptions['replacements']['APP_NAME'])) {
            $this->defaultOptions['replacements']['APP_NAME'] = env('APP_NAME');
        }

        return $this->defaultOptions;
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
