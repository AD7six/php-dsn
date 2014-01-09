<?php

namespace AD7six\Envy;

class CakePHP2Env extends Env {

/**
 * parseCache
 *
 * @param array $defaults
 * @return array
 */
	public static function parseCache($defaults = [], $replacements = []) {
		return static::parsePrefix('CACHE_URL', $defaults, $replacements, [__CLASS__, '_parseCache']);
	}

/**
 * parseDb
 *
 * @param array $defaults
 * @param array $replacements
 * @return array
 */
	public static function parseDb($defaults = [], $replacements = []) {
		return static::parsePrefix('DATABASE_URL', $defaults, $replacements, [__CLASS__, '_parseDb']);
	}

/**
 * parseLogs
 *
 * @param array $defaults
 * @param array $replacements
 * @return array
 */
	public static function parseLogs($defaults = [], $replacements = []) {
		return static::parsePrefix('LOG_URL', $defaults, $replacements, [__CLASS__, '_parseLog'], 'debug');
	}


/**
 * Handler for cache configs
 *
 * @param mixed $key
 * @param mixed $config
 * @param mixed $defaults
 * @return array
 */
	protected static function _parseCache($key, $config, $defaults) {
		$name = isset($config['name']) ? $config['name'] : trim($key, '_');
		$engine = isset($config['engine']) ? $config['engine'] : ucfirst(static::_get($config, 'scheme'));

		$config += [
			'engine' => $engine,
			'serialize' => ($engine === 'File'),
			'login' => static::_get($config, 'user'),
			'password' => static::_get($config, 'pass'),
			'server' => static::_get($config, 'host'),
			'servers' => static::_get($config, 'host')
		] + $defaults;

		return [$name => $config];
	}

/**
 * Handler for db configs
 *
 * @param mixed $key
 * @param mixed $config
 * @param mixed $defaults
 * @return array
 */
	protected static function _parseDb($key, $config, $defaults) {
		$name = isset($config['name']) ? $config['name'] : trim($key, '_');

		$config += [
			'datasource' => 'Database/' . ucfirst(strtolower($config['scheme'])),
			'persistent' => static::_get($config, 'persistent'),
			'host' => static::_get($config, 'host'),
			'login' => static::_get($config, 'user'),
			'password' => static::_get($config, 'pass'),
			'database' => substr($config['path'], 1),
			'persistent' => static::_get($config, 'persistent'),
			'encoding' => static::_get($config, 'encoding') ?: 'utf8'
		] + $defaults;

		return [$name => $config];
	}

/**
 * Handler for log configs
 *
 * @param mixed $key
 * @param mixed $config
 * @param mixed $defaults
 * @return array
 */
	protected static function _parseLog($key, $config, $defaults) {
		$name = isset($config['name']) ? $config['name'] : trim($key, '_');
		$engine = isset($config['engine']) ? $config['engine'] : ucfirst(static::_get($config, 'scheme'));

		$config += [
			'engine' => $engine,
			'file' => $name
		] + $defaults;

		if (isset($config['types']) && !is_array($config['types'])) {
			$config['types'] = explode(',', $config['types']);
		}

		return [$name => $config];
	}
}
