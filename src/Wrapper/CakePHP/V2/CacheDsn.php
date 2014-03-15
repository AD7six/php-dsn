<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V2;

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
            'scheme' => 'engine',
        ],
        'replacements' => [
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
 * getDefaultOptions
 *
 * Add a replacement for DURATION, conditionally set depending on debug,
 *
 * @return array
 */
    protected function getDefaultOptions()
    {
        parent::getDefaultOptions();

        if (!isset($this->defaultOptions['replacements']['DURATION'])) {
            $duration = $this->debug() ? '+10 seconds' : '+999 days';
            $this->defaultOptions['replacements']['DURATION'] = $duration;
        }

        return $this->defaultOptions;
    }

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
