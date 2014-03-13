<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V2;

use AD7six\Dsn\Wrapper\Dsn;

class DbDsn extends Dsn
{

/**
 * Array of scheme => adapters
 *
 * @var array
 */
    protected static $adapterMap = [
        'mssql' => 'Database/Sqlserver',
        'mysql' => 'Database/Mysql',
        'pg' => 'Database/Postgres',
        'pgsql' => 'Database/Posgres',
        'postgres' => 'Database/Posgres',
        'sqlite' => 'Database/Sqlite',
        'sqlite3' => 'Database/Sqlite',
        'sqlserver' => 'Database/Sqlserver',
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
            'engine' => 'datasource',
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
 * getDatasource
 *
 * Get the engine to use for this dsn. Defaults to `Database/Enginename`
 *
 * @return string
 */
    public function getDatasource()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }

        $engine = $this->dsn->engine;

        if (isset(static::$adapterMap[$engine])) {
            return static::$adapterMap[$engine];
        }

        return 'Database/' . ucfirst($engine);
    }

}
