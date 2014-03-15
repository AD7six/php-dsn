<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V3;

use AD7six\Dsn\Wrapper\Dsn;

/**
 * CacheDsn
 *
 */
class CacheDsn extends Dsn
{

/**
 * defaultOptions
 *
 * @var array
 */
    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'className',
        ],
        'replacements' => [
            'APP_NAME' => APP_NAME,
            '/CACHE/' => CACHE
        ]
    ];

/**
 * debug
 *
 * The CakePHP debug setting - cached on first access
 *
 * @var int
 */
    protected $debug = null;

/**
 * debug
 *
 * Get or set the debug value. The debug value is used to determine the default cache duration
 *
 * @param mixed $value
 * @return void
 */
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

/**
 * Get an environment variable
 *
 * If the env function is defined use it, else query $_SERVER, $_ENV and getenv() in that order
 *
 * @param string $key
 * @return mixed
 */
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

/**
 * getDefaultOptions
 *
 * Add a replacement for DURATION, conditionally set depending on debug,
 * and a replacement for APP_NAME, which is used as a prefix
 *
 * @return array
 */
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

/**
 * getClassName
 *
 * Return the adapter if there is one, else return the scheme
 *
 * @return string
 */
    public function getClassName()
    {
        $adapter = $this->getAdapter();

        if ($adapter) {
            return $adapter;
        }

        return ucfirst($this->dsn->scheme);
    }

/**
 * getSerialize
 *
 * @return bool
 */
    public function getSerialize()
    {
        return (bool) $this->dsn->serialize;
    }

/**
 * parse a url as a cache dsn
 *
 * @param string $url
 * @param array $options
 * @return array
 */
    public static function parse($url, $options = [])
    {
        $inst = new CacheDsn($url, $options);
        return $inst->toArray();
    }

}
