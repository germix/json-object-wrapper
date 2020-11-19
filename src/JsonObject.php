<?php

namespace Germix\JsonObjectWrapper;

/**
 * @author Germán Martínez
 */
class JsonObject
{
    private $json;

    public function __construct($data)
    {
        $this->json = [];

        if(is_string($data))
        {
            $data = json_decode($data);
            if(json_last_error() != JSON_ERROR_NONE)
            {
                throw new JsonObjectException('Bad json object');
            }
        }

        if(is_object($data))
        {
            foreach($data as $key => $value)
            {
                if(is_object($value))
                {
                    $this->json[$key] = $this->createJsonObject($value);
                }
                else if(is_array($value))
                {
                    $newArray = [];
                    foreach($value as $k => $v)
                    {
                        if(is_object($v))
                            $newArray[$k] = $this->createJsonObject($v);
                        else
                            $newArray[$k] = $v;
                    }
                    $this->json[$key] = $newArray;
                }
                else
                {
                    $this->json[$key] = $value;
                }
            }
        }
        else
        {
            throw new JsonObjectException('Bad json object');
        }
    }

    /**
     * Returns true if the field is defined.
     */
    public function has($fieldName)
    {
        return \array_key_exists($fieldName, $this->json);
    }

    /**
     * Get a required field
     */
    public function getRequiredField($fieldName)
    {
        if(!array_key_exists($fieldName, $this->json))
        {
            throw new JsonObjectException('"'.$fieldName.'" is required');
        }
        return $this->json[$fieldName];
    }

    /**
     * Get a required field as array
     */
    public function getRequiredFieldAsArray($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!is_array($value))
            throw new JsonObjectException('"'.$fieldName.'" must be an array' . ($nullable ? ' or null' : ''));

        return $value;
    }

    /**
     * Get a required field as object
     */
    public function getRequiredFieldAsObject($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!is_object($value))
            throw new JsonObjectException('"'.$fieldName.'" must be an object' . ($nullable ? ' or null' : ''));

        return $value;
    }

    /**
     * Get a required field as email
     */
    public function getRequiredFieldAsEmail($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!self::isString($value))
            throw new JsonObjectException('"'.$fieldName.'" must be a string' . ($nullable ? ' or null' : ''));

        if(!self::isEmail($value))
        {
            throw new JsonObjectException('"'.$fieldName.'" does not have a valid email format');
        }
        return $value;
    }

    /**
     * Get a required field as string
     */
    public function getRequiredFieldAsString($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!$this->isString($value))
            throw new JsonObjectException('"'.$fieldName.'" must be a string' . ($nullable ? ' or null' : ''));

        return $value;
    }

    /**
     * Get a required field as non empty string
     */
    public function getRequiredFieldAsNonEmptyString($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!self::isString($value))
            throw new JsonObjectException('"'.$fieldName.'" must be a string' . ($nullable ? ' or null' : ''));
        if(empty($value))
            throw new JsonObjectException('"'.$fieldName.'" can\'t be empty');

        return $value;
    }

    /**
     * Get a required field as float
     */
    public function getRequiredFieldAsFloat($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!self::isFloat($value))
            throw new JsonObjectException('"'.$fieldName.'" must be a float' . ($nullable ? ' or null' : ''));

        return floatval($value);
    }

    /**
     * Get a required field as float (greater than zero)
     */
    public function getRequiredFieldAsFloatGreaterThanZero($fieldName, $nullable = false)
    {
        $value = $this->getRequiredFieldAsFloat($fieldName, $nullable);
        if($value !== null)
        {
            if(!($value > 0))
                throw new JsonObjectException('"'.$fieldName.'" must be greater than zero');
        }
        return $value;
    }

    /**
     * Get a required field as integer
     */
    public function getRequiredFieldAsInteger($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!self::isInteger($value))
            throw new JsonObjectException('"'.$fieldName.'" must be an integer' . ($nullable ? ' or null' : ''));

        return intval($value);
    }

    /**
     * Get a required field as integer (greater than zero)
     */
    public function getRequiredFieldAsIntegerGreaterThanZero($fieldName, $nullable = false)
    {
        $value = $this->getRequiredFieldAsInteger($fieldName, $nullable);
        if($value !== null)
        {
            if(!($value > 0))
                throw new JsonObjectException('"'.$fieldName.'" must be greater than zero');
        }
        return $value;
    }

    /**
     * Get a required field as boolean
     */
    public function getRequiredFieldAsBoolean($fieldName, $nullable = false)
    {
        $value = $this->getRequiredField($fieldName);

        if($nullable && $value === null)
            return null;

        if(!self::isBoolean($value))
            throw new JsonObjectException('"'.$fieldName.'" must be a boolean' . ($nullable ? ' or null' : ''));

        return boolval($value);
    }

    /**
     * Check if the value is an email
     */
    private static function isEmail($value) : string
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Check if the value is a string
     */
    private static function isString($value)
    {
        return is_string($value);
    }

    /**
     * Check if the value is a float
     */
    private static function isFloat($value)
    {
        return is_float($value);
    }

    /**
     * Check if the value is an integer
     */
    private static function isInteger($value)
    {
        return (filter_var($value, FILTER_VALIDATE_INT) === 0 || filter_var($value, FILTER_VALIDATE_INT));
    }

    /**
     * Check if the value is a boolean
     */
    private static function isBoolean($value)
    {
        return is_bool($value);
    }

    protected function createJsonObject($value)
    {
        return new JsonObject($value);
    }
}
