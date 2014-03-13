<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V3;

use AD7six\Dsn\Wrapper\Dsn;

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
        'pgsql' => 'Cake\Database\Driver\Posgres',
        'postgres' => 'Cake\Database\Driver\Posgres',
        'sqlite' => 'Cake\Database\Driver\Sqlite',
        'sqlite3' => 'Cake\Database\Driver\Sqlite',
        'sqlserver' => 'Cake\Database\Driver\Sqlserver',
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
            'engine' => 'className',
            'adapter' => false,
            'user' => 'login',
            'pass' => 'password'
        ]
    ];

    public static function parse($url, $options = [])
    {
        $inst = new DbDsn($url, $options);
        return $inst->toArray();
    }

/**
 * getEngine
 *
 * Get the engine to use for this dsn. Defaults to `Cake\Database\Driver\Enginename`
 *
 * @return string
 */
    public function getEngine()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }

        $engine = $this->dsn->engine;

        if (isset(static::$adapterMap[$engine])) {
            return static::$adapterMap[$engine];
        }

        return 'Cake\Database\Driver\\' . ucfirst($engine);
    }

    public function setEngine($value)
    {
        $this->dsn->engine = str_replace('Cake\Database\Driver\\', '', $value);
    }
}
