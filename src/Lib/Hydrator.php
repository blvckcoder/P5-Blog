<?php

namespace App\Lib;

class Hydrator
{
    public static function hydrate(array $data, $object)
    {
        foreach ($data as $key => $value) {
            
            $methodName = 'set' . ucfirst($key);
            
            if (method_exists($object, $methodName)) {
                $object->$methodName($value);
            }
        }

        return $object;
    }
}