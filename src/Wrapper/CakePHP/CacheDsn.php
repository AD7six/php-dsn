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

	public static function parse($url, $options = []) {
		$inst = new CacheDsn($url, $options);
		return $inst->toArray();
	}

	protected function _getDefaultOptions() {
		if (!isset($this->_defaultOptions['replacements']['DURATION'])) {
			$duration = \Configure::read('debug') ? '+10 seconds' : '+999 days';
			$this->_defaultOptions['replacements']['DURATION'] = $duration;
		}

		if (!isset($this->_defaultOptions['replacements']['APP_NAME'])) {
			$this->_defaultOptions['replacements']['APP_NAME'] = env('APP_NAME');
		}

		return $this->_defaultOptions;
	}

	public function getEngine() {
		$adapter = $this->_dsn->adapter;

		if ($adapter) {
			return $adapter;
		}
		return ucfirst($this->_dsn->scheme);
	}

	public function setEngine($value) {
		$this->_dsn->scheme = lcfirst($value);
	}

}
