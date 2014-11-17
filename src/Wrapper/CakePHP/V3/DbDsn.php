<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V3;

use AD7six\Dsn\Wrapper\Dsn;

/**
 * DbDsn
 *
 */
class DbDsn extends Dsn
{

/**
 * Array of scheme => adapters
 *
 * @var array
 */
    protected static $adapterMap = [
        'mssql' => 'Cake\Database\Driver\Sqlserver',
        'mysql' => 'Cake\Database\Driver\Mysql',
        'pg' => 'Cake\Database\Driver\Postgres',
        'pgsql' => 'Cake\Database\Driver\Postgres',
        'postgres' => 'Cake\Database\Driver\Postgres',
        'sqlite' => 'Cake\Database\Driver\Sqlite',
        'sqlite3' => 'Cake\Database\Driver\Sqlite',
        'sqlserver' => 'Cake\Database\Driver\Sqlserver',
    ];

/**
 * Mandatory keys
 *
 * className must be in responses
 */
    protected static $mandatoryKeys = [
        'className'
    ];

/**
 * The keymap for CakePHP db dsns.
 *
 * Adapter is false because it's not required in the resultant array
 *
 * @var array
 */
    protected $defaultOptions = [
        'keyMap' => [
            'engine' => 'driver',
            'user' => 'username',
            'pass' => 'password'
        ]
    ];

/**
 * getClassName
 *
 * @return string
 */
    public function getClassName()
    {
        return $this->dsn->className ?: 'Cake\Database\Connection';
    }

/**
 * getDriver
 *
 * Get the engine to use for this dsn. Defaults to `Cake\Database\Driver\Enginename`
 *
 * @return string
 */
    public function getDriver()
    {
        $adapter = $this->getAdapter();

        if ($adapter) {
            return $adapter;
        }

        $engine = $this->dsn->engine;

        return 'Cake\Database\Driver\\' . ucfirst($engine);
    }

/**
 * parse a url as a database dsn
 *
 * @param string $url
 * @param array $options
 * @return array
 */
    public static function parse($url, $options = [])
    {
        $inst = new DbDsn($url, $options);
        return $inst->toArray();
    }
}
