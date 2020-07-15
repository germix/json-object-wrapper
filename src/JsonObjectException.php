<?php

namespace Germix\JsonObjectWrapper;

/**
 * @author Germán Martínez
 */
class JsonObjectException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
