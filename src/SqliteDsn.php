<?php

namespace AD7six\Dsn;

/**
 * SqliteDsn
 *
 */
class SqliteDsn extends DbDsn {

/**
 * The database in an sqlite dsn is a file
 *
 * @var bool
 */
	protected $_databaseIsFile = true;
}
