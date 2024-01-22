<?php

declare(strict_types=1);

namespace App\Lib;

class Hydrator
{
    public static function hydrate(array $data, object $object): object
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