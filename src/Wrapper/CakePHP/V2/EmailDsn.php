<?php

namespace AD7six\Dsn\Wrapper\CakePHP\V2;

use AD7six\Dsn\Wrapper\Dsn;

class EmailDsn extends Dsn
{

    protected $defaultOptions = [
        'keyMap' => [
            'scheme' => 'transport',
            'user' => 'username',
            'pass' => 'password',
        ]
    ];

    public static function parse($url, $options = [])
    {
        $inst = new EmailDsn($url, $options);
        return $inst->toArray();
    }

    public function getPath()
    {
        return null;
    }

    public function getTimeout()
    {
        $timeout = $this->dsn->timeout;

        if ($timeout === null) {
            return;
        }

        return (int) $timeout;
    }

    public function getScheme()
    {
        $adapter = $this->dsn->adapter;

        if ($adapter) {
            return $adapter;
        }
        return ucfirst($this->dsn->scheme);
    }

    public function getMessageId()
    {
        $return = $this->dsn->messageId;

        if ($return === null) {
            return;
        }

        if (strlen($return) > 1) {
            return $return;
        }

        return (bool) $return;
    }

    public function getTemplate()
    {
        $return = $this->dsn->template;

        if ($return === null) {
            return;
        }

        return (bool) $return;
    }

    public function getLayout()
    {
        $return = $this->dsn->layout;

        if ($return === null) {
            return;
        }

        return (bool) $return;
    }


    public function setScheme($value)
    {
        $this->dsn->scheme = lcfirst($value);
    }
}
