<?php

namespace AD7six\Dsn\Wrapper\CakePHP;

use AD7six\Dsn\Wrapper\Dsn;

class CacheDsn extends Dsn {

	protected $_defaultOptions = [
		'keyMap' => [
			'scheme' => 'engine',
		],
		'replacements' => [
			'/CACHE/' => CACHE
		]
	];

	protected function _getDefaultOptions() {
		if (!isset($this->_defaultOptions['replacements']['DURATION'])) {
			$duration = \Configure::read('debug') ? '+10 seconds' : '+999 days';
			$this->_defaultOptions['replacements']['DURATION'] = $duration;
		}

		return $this->_defaultOptions;
	}

	public function getEngine() {
		return ucfirst($this->_dsn->scheme);
	}

	public function setEngine($value) {
		$this->_dsn->scheme = lcfirst($value);
	}

}
