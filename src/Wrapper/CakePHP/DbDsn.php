<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class DbDsn extends Dsn {

	protected $_defaultOptions = [
		'keyMap' => [
			'engine' => 'datasource',
			'user' => 'login',
			'pass' => 'password'
		]
	];

	public static function parse($url, $options = []) {
		$inst = new DbDsn($url, $options);
		return $inst->toArray();
	}

	public function getDatasource() {
		$adapter = $this->_dsn->adapter;

		if ($adapter) {
			return $adapter;
		}
		return 'Database/' . ucfirst($this->_dsn->engine);
	}

	public function setDatasource($value) {
		$this->_dsn->engine = str_replace('Database/', '', $value);
	}

}
