<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V3;

use AD7six\Dsn\Wrapper\Dsn;

class CacheDsn extends Dsn
{

    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'className',
        ],
        'replacements' => [
            '/CACHE/' => CACHE
        ]
    ];

    protected $debug = null;

    public function env($key = null)
    {
        if (function_exists('env')) {
            return env($key);
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        } elseif (isset($_ENV[$key])) {
            return $_ENV[$key];
        } elseif (getenv($key) !== false) {
            return getenv($key);
        }

        return null;
    }

    public function debug($value = null)
    {
        if ($value !== null) {
            $this->debug = $value;
        } elseif ($value === null && $this->debug === null) {
            $this->debug = 0;
            if (class_exists('\Configure')) {
                $this->debug = (int) \Configure::read('debug');
            }
        }

        return $this->debug;
    }

    public static function parse($url, $options = [])
    {
        $inst = new CacheDsn($url, $options);
        return $inst->toArray();
    }

    protected function getDefaultOptions()
    {
        if (!isset($this->defaultOptions['replacements']['DURATION'])) {
            $duration = $this->debug() ? '+10 seconds' : '+999 days';
            $this->defaultOptions['replacements']['DURATION'] = $duration;
        }

        if (!isset($this->defaultOptions['replacements']['APP_NAME'])) {
            $this->defaultOptions['replacements']['APP_NAME'] = $this->env('APP_NAME');
        }

        return $this->defaultOptions;
    }

    public function getScheme()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }
        return ucfirst($this->dsn->scheme);
    }

    public function setScheme($value)
    {
        $this->dsn->scheme = lcfirst($value);
    }

    public function getSerialize()
    {
        return (bool) $this->dsn->serialize;
    }
}
