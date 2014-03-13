<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V2;

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
 * Adapter is false to prevent it appearing in this wrapper's array representation
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

/**
 * getDatasource
 *
 * Get the engine to use for this dsn. Defaults to `Database/Enginename`
 *
 * @return string
 */
    public function getDatasource()
    {
        $adapter = $this->getAdapter();

        if ($adapter) {
            return $adapter;
        }

        $engine = $this->dsn->engine;

        return 'Database/' . ucfirst($engine);
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
