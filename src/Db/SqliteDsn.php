<?php

namespace AD7six\Dsn\Db;

use AD7six\Dsn\DbDsn;

/**
 * SqliteDsn
 *
 */
class SqliteDsn extends DbDsn
{

/**
 * The database in a sqlite dsn is a path, not a database name
 *
 * @var bool
 */
    protected $databaseIsPath = true;
}
