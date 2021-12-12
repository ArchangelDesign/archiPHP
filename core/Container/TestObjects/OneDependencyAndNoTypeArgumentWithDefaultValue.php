<?php

namespace Archi\Container\TestObjects;

/**
 * Class OneDependencyAndNoTypeArgumentWithDefaultValue
 * Autowire test of the class that has one dependency and
 * one typeless argument with default value.
 * This class can be autowired.
 */
class OneDependencyAndNoTypeArgumentWithDefaultValue
{
    public function __construct(NoDependencyClass $dep, $s = '')
    {
    }
}
