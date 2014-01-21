<?php

namespace AD7six\Dsn;

/**
 * SqliteDsn
 *
 */
class SqliteDsn extends DbDsn {

/**
 * The path in a sqlite dsn is a path, not a database name
 *
 * @var bool
 */
	protected $_databaseIsFile = true;
}
