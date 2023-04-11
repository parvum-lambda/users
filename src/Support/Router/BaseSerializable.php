<?php

namespace Support\Router;

abstract class BaseSerializable
{
    public function toArray() : array
    {
        $result = [];

        foreach (get_object_vars($this) as $name => $value) {
            if ($this->{$name} !== null) {
                $result[$name] = $value;
            }
        }

        return $result;
    }
}
