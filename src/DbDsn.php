<?php

namespace AD7six\Dsn;

class DbDsn extends Dsn {

	protected $_defaultPort;

	protected $_keyMap = [
		'path' => 'database'
	];

	public function keyMap($keyMap = null) {
		if ($keyMap) {
			$keyMap += ['path' => 'database'];
		}
		return parent::keyMap($keyMap);
	}

	public function parseUrl($string) {
		$return = parent::parseUrl($string);

		if (isset($this->_url['path'])) {
			$this->_url['path'] = ltrim($this->_url['path'], '/');
		}
	}

	protected function _toUrl($data) {
		if (isset($data['path'])) {
			$data['path'] = '/' . $data['path'];
		}
		return parent::_toUrl($data);
	}
}
