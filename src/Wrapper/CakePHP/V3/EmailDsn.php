<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V3;

use AD7six\Dsn\Wrapper\Dsn;

/**
 * EmailDsn
 *
 */
class EmailDsn extends Dsn
{

/**
 * defaultOptions
 *
 * @var array
 */
    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'className',
            'user' => 'username',
            'pass' => 'password',
            'path' => false
        ]
    ];

/**
 * getClassName
 *
 * Return the adapter if there is one, else return the scheme
 *
 * @return string
 */
    public function getClassName()
    {
        $adapter = $this->getAdapter();

        if ($adapter) {
            return $adapter;
        }

        return ucfirst($this->dsn->scheme);
    }

/**
 * getLayout
 *
 * Return the layout, if defined, as either a string or bool as appropriate
 *
 * @return mixed
 */
    public function getLayout()
    {
       $return = $this->dsn->layout;

        if ($return === null) {
            return;
        }

        if (!$return) {
            return false;
        }

        return $return;
    }

 /**
 * getMessageId
 *
 * Return the message id, if defined, as either a string or bool as appropriate
 *
 * @return mixed
 */
    public function getMessageId()
    {
       $return = $this->dsn->messageId;

        if ($return === null) {
            return;
        }

        if ($return === '1') {
            return true;
        }

        if ($return === '0') {
            return false;
        }

        return $return;
    }

/**
 * getTemplate
 *
 * Return the template, if defined, as either a string or bool as appropriate
 *
 * @return mixed
 */
    public function getTemplate()
    {
        $return = $this->dsn->template;

        if ($return === null) {
            return;
        }

        if (!$return) {
            return false;
        }

        return $return;
    }

/**
 * getTimeout
 *
 * If specified, return the timeout as an integer
 *
 * @return int
 */
    public function getTimeout()
    {
        $timeout = $this->dsn->timeout;

        if ($timeout === null) {
            return;
        }

        return (int) $timeout;
    }
/**
 * parse a url as an email dsn
 *
 * @param string $url
 * @param array $options
 * @return array
 */
   public static function parse($url, $options = [])
   {
        $inst = new EmailDsn($url, $options);
        return $inst->toArray();
    }
}
