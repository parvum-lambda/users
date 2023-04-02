<?php

namespace Support\Attributes\Router\Options;

abstract class Option
{
    public function __invoke(array &$options)
    {
        $key = $this->getKey();
        $value = $this->getValue();

        if (! is_array($value)) {
            if (! isset($options[$key])) {
                $options[$key] = $value;
            }
        } else {
            if (! isset($options[$key])) {
                $options[$key] = $value;
            } elseif (! is_array($options[$key])) {
                $options[$key] = [$options[$key], ...$value];
            } else {
                $options[$key] = array_merge($options[$key], $value);
            }
        }
    }

    abstract public function getKey() : string;

    abstract public function getValue() : string | array;
}
